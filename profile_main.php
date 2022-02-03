<?php

    function AlunoProfileMain($id_aluno) {


        $conn = bd_connect_livel();

        if (!$conn) {
            $err_msg = 'N&atilde;o foi poss&iacute;vel estabelecer conex&atilde;o.';

        } else {

            $arr_perfil = array();

            $str_sql = "SELECT 
            c001_alunos.c001_id_aluno_lo AS AlunoID,
            c001_alunos.c001_id_cliente_box AS AlunoFitboxID,
            c001_alunos.c001_nome_completo AS AlunoNome,
            c001_alunos.c001_aluno_ativo AS AlunoAtivo,
            c001_alunos.c001_celular AS AlunoCelular,
            c001_alunos.c001_email AS AlunoEmail,
            
            (CASE 
                WHEN c001_alunos.c001_arquivo_foto IS NOT NULL 
                THEN CONCAT('https://fitgroup.com.br/livel_fitbox/assets/' ,c001_alunos.c001_arquivo_foto) 
                ELSE 'https://fitgroup.com.br/livel_fitbox/assets/icons/profile.png' 
                END) AS AlunoFoto,
            
            lo_vendas.lo_id_venda AS VendaID, 
            UCASE(lo_venda_tipos.lo_venda_tipo_descricao) VendaTipo, 

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
                    CONCAT(lo_produtos_unidades_venda.lo_unidade_descricao, ' (', lo_vendas.lo_venda_qtde, ')')
                END  
                AS VigenciaDescricao,
            
            (SELECT 
            MAX(lo_venda_itens.lo_item_vigencia_fim) 
            FROM 
            lo_venda_itens 
            WHERE 
            lo_venda_itens.lo_id_venda = lo_vendas.lo_id_venda 
            ) AS VendaVigenciaFim
            
            FROM c001_alunos
            LEFT OUTER JOIN lo_vendas ON lo_vendas.c001_id_aluno_lo = c001_alunos.c001_id_aluno_lo
            LEFT OUTER JOIN lo_venda_tipos ON lo_vendas.lo_id_venda_tipo = lo_venda_tipos.lo_id_venda_tipo 
            LEFT OUTER JOIN lo_produtos_unidades_venda ON lo_produtos_unidades_venda.lo_id_unidade = lo_vendas.lo_id_unidade
            WHERE 
            c001_alunos.c001_id_aluno_lo = $id_aluno 
            
            ORDER BY lo_vendas.lo_id_venda DESC 
            LIMIT 1";

            $rs_perfil = mysqli_query($conn, $str_sql);	   
            $num_perfil = mysqli_num_rows($rs_perfil);  

            while($r = mysqli_fetch_assoc($rs_perfil)) {
                $arr_perfil = $r;
            }                                     
        }

        $arr_result = array('Registros'=>$num_perfil,'Perfil'=>$arr_perfil, 'Erro'=>$err_msg);

        return $arr_result;
    }
?>