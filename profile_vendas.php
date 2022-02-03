<?php

    require_once 'classes/crypt.php';

    function AlunoProfileVendas($id_aluno) {


        $conn = bd_connect_livel();

        if (!$conn) {
            $err_msg = 'N&atilde;o foi poss&iacute;vel estabelecer conex&atilde;o.';

        } else {

            $arr_vendas = array();

            $str_venda = "SELECT 
                lo_vendas.lo_id_venda AS VendaID, 
                lo_venda_status.lo_id_venda_status AS VendaStatusID,
                @venda_tipo := lo_vendas.lo_id_venda_tipo AS VendaTipoID,
                UCASE(lo_venda_tipos.lo_venda_tipo_descricao) AS VendaTipoDescricao, 

                CASE 
                    WHEN (lo_vendas.lo_id_unidade = 2)
                    THEN
                        (CASE 
                                WHEN lo_vendas.lo_venda_qtde = 1 THEN 'MENSAL' 
                                WHEN lo_vendas.lo_venda_qtde = 2 THEN 'BIMESTRAL'
                                WHEN lo_vendas.lo_venda_qtde = 3 THEN 'TRIMESTRAL'
                                WHEN lo_vendas.lo_venda_qtde = 6 THEN 'SEMESTRAL'
                                WHEN lo_vendas.lo_venda_qtde = 12 THEN 'ANUAL'
                                ELSE CONCAT(lo_vendas.lo_venda_qtde,' MESES')
                        END)
                    ELSE
                        CONCAT(lo_vendas.lo_venda_qtde,' ', lo_produtos_unidades_venda.lo_unidade_descricao)
                    END  
                AS VendaVigenciaDescricao,
                
                lo_vendas.lo_venda_data AS VendaData,
                lo_venda_status.lo_venda_status_descricao AS VendaStatus,

                (SELECT 
                lo_planos.lo_plano_nome 
                FROM 
                lo_venda_itens  
                INNER JOIN lo_plano_vigencias ON lo_plano_vigencias.lo_id_plano_vigencia = lo_venda_itens.lo_id_plano_vigencia
                INNER JOIN lo_planos ON lo_planos.lo_id_plano = lo_plano_vigencias.lo_id_plano
                WHERE 
                lo_venda_itens.lo_id_venda = lo_vendas.lo_id_venda 
                LIMIT 1
                ) AS VendaPlanoNome,
                                
                (SELECT 
                MIN(lo_venda_itens.lo_item_vigencia_inicio) 
                FROM 
                lo_venda_itens 
                WHERE 
                lo_venda_itens.lo_id_venda = lo_vendas.lo_id_venda 
                ) AS VendaVigenciaInicial,
                
                (SELECT 
                MAX(lo_venda_itens.lo_item_vigencia_fim) 
                FROM 
                lo_venda_itens 
                WHERE 
                lo_venda_itens.lo_id_venda = lo_vendas.lo_id_venda 
                ) AS VendaVigenciaFinal,

                (SELECT
                COUNT(lo_transacoes.lo_id_transacao)
                FROM lo_transacoes
                WHERE lo_transacoes.lo_transacao_vencimento < DATE(NOW()) 
                AND (lo_transacoes.cs019b_id_spay_transacao_status NOT IN (1,31,99) OR lo_transacoes.cs019b_id_spay_transacao_status IS NULL)
                AND lo_transacoes.lo_id_venda = lo_vendas.lo_id_venda
                ) AS VendaPendenciaFinan,

                @parcelas_restantes := (SELECT
                COUNT(lo_transacoes.lo_id_transacao)
                FROM lo_transacoes
                WHERE lo_transacoes.lo_transacao_vencimento > DATE(NOW()) 
                AND lo_transacoes.lo_id_venda = lo_vendas.lo_id_venda
                ) AS ParcelasRestantes,

                IF(((@venda_tipo = 3 AND @parcelas_restantes = 0) OR (lo_vendas.lo_id_unidade = 2 AND lo_vendas.lo_venda_qtde = 1)) , 1, 0) AS CancelEnabled,
                lo_vendas.lo_id_aluno_cc AS CartaoID        
                
                FROM 
                lo_vendas 
                INNER JOIN lo_venda_tipos ON lo_vendas.lo_id_venda_tipo = lo_venda_tipos.lo_id_venda_tipo 
                INNER JOIN lo_venda_status ON lo_venda_status.lo_id_venda_status = lo_vendas.lo_id_venda_status
                INNER JOIN lo_produtos_unidades_venda ON lo_produtos_unidades_venda.lo_id_unidade = lo_vendas.lo_id_unidade
                WHERE 
                lo_vendas.c001_id_aluno_lo = " . $id_aluno . "
                ORDER BY lo_vendas.lo_venda_data DESC";

            $rs_venda = mysqli_query($conn, $str_venda);	   
            $num_venda = mysqli_num_rows($rs_venda);  

            $i=0;

            while($r = mysqli_fetch_assoc($rs_venda)) {
                $arr_vendas[$i] = $r;

                //DADOS CARTAO
                $cartao_id = $r['CartaoID'];
                
                $str_sql = "SELECT
                    lo_aluno_cc.lo_id_aluno_cc,
                    lo_aluno_cc.lo_cc_numero,
                    lo_aluno_cc.lo_cc_validade,                
                    cs009e_cartao_administradora.cs009e_admnistradora_nome,
                    cs019a_spay_cartoes.cs009e_id_admnistradora
                    FROM
                    lo_aluno_cc 
                    INNER JOIN cs019a_spay_cartoes ON lo_aluno_cc.cs019a_id_spay_cartao = cs019a_spay_cartoes.cs019a_id_spay_cartao
                    INNER JOIN cs009e_cartao_administradora ON cs019a_spay_cartoes.cs009e_id_admnistradora = cs009e_cartao_administradora.cs009e_id_admnistradora
                    WHERE
                    lo_aluno_cc.lo_id_aluno_cc = " .  $cartao_id;

                $rs_cartao = mysqli_query($conn, $str_sql);	   
                $num_cartao = mysqli_num_rows($rs_cartao);  
                $arr_cartao = array();

                while($rc = mysqli_fetch_assoc($rs_cartao)) {
                    $cartao_numero = substr(CryptStr($rc['lo_cc_numero'],'undo'), -4);
                    $cartao_bandeira = 'http://fitgroup.com.br/livel_fitbox/assets/cc_bandeiras/' . $rc['cs009e_id_admnistradora'] . '_32.png';
                    $cartao_validade = date_create($rc['lo_cc_validade']);
                    $cartao_validade  = date_format($cartao_validade, 'm/Y');
                    $cartao_vencido = (DateDifDays($rc['lo_cc_validade']) < 0 ? true : false);

                    $arr_cartao = array(
                        'BandeiraImg' => $cartao_bandeira,
                        'CartaoNumero' => $cartao_numero,
                        'CartaoValidade' => $cartao_validade,
                        'CartaoVencido' => $cartao_vencido
                    );                    
                }
                
                $arr_vendas[$i]['CartaoInfo'] = $arr_cartao;


                //DADOS ÍTENS VENDA
                $str_sql = "SELECT
                        (IF(lo_produtos.lo_id_live IS NOT NULL, lo_lives.lo_live_nome,
                            IF(lo_produtos.lo_id_treino_guiado IS NOT NULL,lo_treinos_guiados.lo_treino_guiado_nome,
                            IF(lo_produtos.lo_id_conteudo IS NOT NULL,CONCAT('Conteúdo: ', lo_conteudos.lo_conteudo_nome),'')))
                        ) AS ItemNome,

                        (CASE 
                                WHEN lo_produtos.lo_id_produto_categoria = 1 THEN 'https://fitgroup.com.br/livel_fitbox/assets/icons/item_live.png' 
                                WHEN lo_produtos.lo_id_produto_categoria = 2 THEN 'https://fitgroup.com.br/livel_fitbox/assets/icons/item_treino.png' 
                                WHEN lo_produtos.lo_id_produto_categoria= 3 THEN 'https://fitgroup.com.br/livel_fitbox/assets/icons/item_conteudo.png'
                        END
                        ) AS ItemImg

                        FROM
                        lo_venda_itens
                        LEFT OUTER JOIN lo_produtos_valores ON lo_venda_itens.lo_id_produto_valor = lo_produtos_valores.lo_id_produto_valor
                        LEFT OUTER JOIN lo_produtos ON lo_produtos_valores.lo_id_produto = lo_produtos.lo_id_produto
                        LEFT OUTER JOIN lo_lives_turmas ON lo_venda_itens.lo_id_live_turma = lo_lives_turmas.lo_id_live_turma
                        LEFT OUTER JOIN lo_lives ON lo_lives_turmas.lo_id_live = lo_lives.lo_id_live
                        LEFT OUTER JOIN lo_conteudos ON lo_produtos.lo_id_conteudo = lo_conteudos.lo_id_conteudo
                        LEFT OUTER JOIN lo_treinos_guiados ON lo_produtos.lo_id_treino_guiado = lo_treinos_guiados.lo_id_treino_guiado
                        WHERE
                        lo_venda_itens.lo_id_venda = " . $venda_id = $r['VendaID'];

                $rs_itens = mysqli_query($conn, $str_sql);	   
                $num_itens = mysqli_num_rows($rs_itens);  
                $arr_itens = array();

                while($ri = mysqli_fetch_assoc($rs_itens)) {
                    $arr_itens[] = $ri;                 
                }

                $arr_vendas[$i]['ItensVenda'] = $arr_itens;

                $i++;
            }                                     
        }

        $arr_result = array('Registros'=>$num_venda,'Vendas'=>$arr_vendas, 'Erro'=>$err_msg);

        return $arr_result;
    }
?>