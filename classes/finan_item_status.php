<?php

    function AlunoAcessos($id_aluno, $aula_tipo) {
        //$aula_tipo = 'aula_live' / 'treinos_guiados'

        $conn = bd_connect_livel();

        if (!$conn) {
            return false;  
        
        } else if (!$id_aluno && !$aula_tipo) {
            return false;  
        }

        $campo_categoria = ($aula_tipo == 'aula_live' ? "lo_produtos.lo_id_live" : "lo_produtos.lo_id_treino_guiado");
        //$item_array = ($aula_tipo == 'aula_live' ? "Aulas_Live" : "Treinos_Guiados");

        $str_sql = "SELECT
            lo_vendas.c001_id_aluno_lo,
            lo_vendas.lo_id_venda_status,
            lo_venda_status.lo_venda_status_descricao,
            $campo_categoria,
            lo_produtos.lo_id_live,
            lo_venda_itens.lo_item_quantidade AS ItemQuantidade,
            lo_produtos_valores.lo_id_unidade,
            DATEDIFF(lo_venda_itens.lo_item_vigencia_fim, DATE(NOW())) AS DiasRestantes,
            
            (SELECT
            COUNT(lo_transacoes.lo_id_transacao)
            FROM lo_transacoes
            WHERE lo_transacoes.lo_transacao_vencimento < DATE(NOW()) 
            AND (lo_transacoes.cs019b_id_spay_transacao_status NOT IN (1,31,99) OR lo_transacoes.cs019b_id_spay_transacao_status IS NULL)
            AND lo_transacoes.lo_id_venda = lo_vendas.lo_id_venda
            ) AS PendenciaFinan,
            
            (SELECT
                COUNT(lo_acessos.lo_id_acesso)
                FROM lo_acessos
                WHERE lo_acessos.lo_id_venda_item = lo_venda_itens.lo_id_venda_item
                ) AS ItemNumAcessos
            
            FROM
            lo_venda_itens
            INNER JOIN lo_vendas ON lo_venda_itens.lo_id_venda = lo_vendas.lo_id_venda
            INNER JOIN lo_produtos_valores ON lo_venda_itens.lo_id_produto_valor = lo_produtos_valores.lo_id_produto_valor
            INNER JOIN lo_produtos ON lo_produtos_valores.lo_id_produto = lo_produtos.lo_id_produto
            INNER JOIN lo_venda_status on lo_venda_status.lo_id_venda_status = lo_vendas.lo_id_venda_status
            HAVING
            lo_vendas.c001_id_aluno_lo = " . $id_aluno . " 
            AND $campo_categoria IS NOT NULL
            ORDER BY lo_venda_itens.lo_item_vigencia_fim DESC
            LIMIT 1";
//echo $str_sql;
        $rs_aula = mysqli_query($conn, $str_sql);	   
        $num_aula = mysqli_num_rows($rs_aula);    

        if ($num_aula > 0){

            while($r = mysqli_fetch_assoc($rs_aula)) {

                $venda_status = $r['lo_id_venda_status'];
                $has_pendencia = ($r['PendenciaFinan'] > 0 ? true : false);
                
                if ($venda_status == 1) {
                    if ($r['lo_id_unidade'] == 1 || $r['lo_id_unidade'] == 2) {
                        $has_acesso = ($r['DiasRestantes'] >= 0 ? true : false);
                    
                    } else if ($r['lo_id_unidade'] == 3) {
                        $has_acesso = ($r['ItemNumAcessos'] < $r['ItemQuantidade'] ? true : false); 
                    }

                } else {
                    $has_acesso = false;
                }   
            }  

        } else {
            $has_acesso = false;
            $has_pendencia = false;
        }   
            
        $arr_aulas = array('Acesso' => $has_acesso, 
                        'Pendencia' => $has_pendencia, 
                        'VendaStatus' => $venda_status);


        return $arr_aulas;
    }

    function ConteudoFinanStatus($id_aluno, $id_produto) {

        $conn = bd_connect_livel();

        if (!$conn) {
            goto NO_RESULT;

        } else {
            //CHECA SE EXISTE O PRODUTO FREE
            $str_sql = "SELECT COUNT(*) AS ItemFree
                FROM lo_conteudos
                INNER JOIN lo_produtos ON lo_conteudos.lo_id_conteudo = lo_produtos.lo_id_conteudo
                INNER JOIN lo_produtos_valores ON  lo_produtos.lo_id_produto = lo_produtos_valores.lo_id_produto
                WHERE lo_produtos.lo_id_produto = $id_produto 
                AND lo_conteudos.lo_conteudo_data_exclusao IS NULL 
                AND lo_produtos_valores.lo_id_unidade = 5";

            $rs_itens = mysqli_query($conn, $str_sql);	   
            $num_itens = mysqli_num_rows($rs_itens);    
    
            if ($num_itens > 0){
                while($r = mysqli_fetch_assoc($rs_itens)) {
                    if ($r['ItemFree'] > 0) {
                        $arr_result = array('ConteudoAcesso'=>true,
                                            'ConteudoPendenciaFinan'=>false);

                        goto RESULT;

                    }
                }   
            }

            //CHECA SE O ALUNO TEM ACESSO AO PRODUTO E SE ESTÁ EM DIA
            $str_sql = "SELECT
                c001_alunos.c001_id_aluno_lo AS AlunoID,
                c001_alunos.c001_id_cliente_box AS AlunoFitboxID,
                lo_vendas.c001_id_aluno_lo AS AlunoResponsavelID,
                lo_vendas.lo_id_venda AS VendaID,
                lo_venda_itens.lo_id_venda_item AS VendaItemID,
                lo_produtos_categorias.lo_id_produto_categoria AS ProdutoCategoriaID,
                lo_produtos_unidades_venda.lo_id_unidade AS ProdutoUnidadeID,
                lo_venda_itens.lo_item_vigencia_inicio AS ItemVigenciaInicio,
                lo_venda_itens.lo_item_vigencia_fim AS ItemVigenciaFim,
                lo_venda_itens.lo_item_quantidade AS ItemQuantidade,
                (SELECT
                COUNT(lo_acessos.lo_id_acesso)
                FROM lo_acessos
                WHERE lo_acessos.lo_id_venda_item = lo_venda_itens.lo_id_venda_item
                ) AS ItemNumAcessos,
                (SELECT
                COUNT(lo_transacoes.lo_id_transacao)
                FROM lo_transacoes
                WHERE lo_transacoes.lo_transacao_vencimento < DATE(NOW()) 
                AND (lo_transacoes.cs019b_id_spay_transacao_status NOT IN (1,31,99) OR lo_transacoes.cs019b_id_spay_transacao_status IS NULL)
                AND lo_transacoes.lo_id_venda = lo_vendas.lo_id_venda
                ) AS PendenciaFinan
                FROM
                lo_venda_itens
                INNER JOIN lo_vendas ON lo_venda_itens.lo_id_venda = lo_vendas.lo_id_venda
                LEFT OUTER JOIN lo_produtos_valores ON lo_venda_itens.lo_id_produto_valor = lo_produtos_valores.lo_id_produto_valor
                LEFT OUTER JOIN lo_produtos ON lo_produtos_valores.lo_id_produto = lo_produtos.lo_id_produto
                LEFT OUTER JOIN lo_produtos_categorias ON lo_produtos.lo_id_produto_categoria = lo_produtos_categorias.lo_id_produto_categoria
                LEFT OUTER JOIN c001_alunos ON lo_vendas.c001_id_aluno_lo = c001_alunos.c001_id_aluno_lo
                INNER JOIN lo_produtos_unidades_venda ON lo_produtos_valores.lo_id_unidade = lo_produtos_unidades_venda.lo_id_unidade
                WHERE lo_produtos.lo_id_produto =  " . $id_produto . "
                HAVING AlunoID = " . $id_aluno . "
                AND (
                (ProdutoUnidadeID IN (1,2) 
                    AND DATE(NOW()) BETWEEN ItemVigenciaInicio AND ItemVigenciaFim)
                    AND ItemVigenciaInicio <= DATE(NOW())
                )
                OR
                (ProdutoUnidadeID = 3 AND (ItemNumAcessos < ItemQuantidade OR ItemNumAcessos IS NULL))
                OR ProdutoUnidadeID = 4 
                
                ORDER BY lo_vendas.lo_id_venda DESC 
                LIMIT 1";

            $rs_itens = mysqli_query($conn, $str_sql);	   
            $num_itens = mysqli_num_rows($rs_itens);    

            if ($num_itens > 0){
                while($r = mysqli_fetch_assoc($rs_itens)) {
                    $arr_result = array('ConteudoAcesso'=>true,
                                        'ConteudoPendenciaFinan'=>($r['PendenciaFinan']>0 ? true:false));
                    
                    goto RESULT;
                }

            } else {
                goto NO_RESULT;

            }
        
        }

NO_RESULT:
    $arr_result = array('ConteudoAcesso'=>false,
                        'ConteudoPendenciaFinan'=>false);
    goto RESULT;

RESULT:                        
        return $arr_result;
    }

?>