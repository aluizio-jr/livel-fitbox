<?php

    function AlunoPerfilMain($id_aluno) {


        $conn = bd_connect_livel();

        if (!$conn) {
            $err_msg = 'N&atilde;o foi poss&iacute;vel estabelecer conex&atilde;o.';

        } else {

            $arr_perfil = array();

            $str_sql = "SELECT 
            c001_alunos.c001_id_aluno_lo AS AlunoID,
            c001_alunos.c001_id_cliente_box AS AlunoIDFitbox,
            c001_alunos.c001_nome_completo AS AlunoNome,
            c001_alunos.c001_aluno_ativo AS AlunoAtivo,
            c001_alunos.c001_celular AS AlunoCelular,
            c001_alunos.c001_email AS AlunoEmail,
            
            (CASE 
                WHEN c001_alunos.c001_arquivo_foto IS NOT NULL 
                THEN CONCAT('http://fitgroup.com.br/livel_fitbox/assets/' ,c001_alunos.c001_arquivo_foto) 
                ELSE NULL 
                END) AS AlunoFoto,
            
            lo_vendas.lo_id_venda AS VendaID, 
            lo_vendas.lo_venda_perfis VendaPerfis, 
            UCASE(lo_venda_tipos.lo_venda_tipo_descricao) VendaTipo, 
            
            (CASE 
                WHEN lo_vendas.lo_venda_vigencia = 1 THEN 'MENSAL' 
                WHEN lo_vendas.lo_venda_vigencia = 2 THEN 'BIMESTRAL'
                WHEN lo_vendas.lo_venda_vigencia = 3 THEN 'TRIMESTRAL'
                WHEN lo_vendas.lo_venda_vigencia = 6 THEN 'SEMESTRAL'
                WHEN lo_vendas.lo_venda_vigencia = 12 THEN 'ANUAL'
                ELSE CONCAT(lo_vendas.lo_venda_vigencia,'MESES')
                END
            ) AS VendaVigenciaDescricao,
            
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
            WHERE 
            c001_alunos.c001_id_aluno_lo = " . $id_aluno . "
            AND lo_vendas.lo_id_venda_status
            
            ORDER BY lo_vendas.lo_id_venda DESC 
            LIMIT ";

            $rs_perfil = mysqli_query($conn, $str_sql);	   

            while($r = mysqli_fetch_assoc($rs_perfil)) {
                $arr_perfil = $r;
            }                                     
        }

        $arr_result = array('Registros'=>count($arr_perfil),'PerfilMain'=>$arr_perfil, 'Erro'=>$err_msg);

        return $arr_result;
    }
?>