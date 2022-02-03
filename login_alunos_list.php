<?php

    function LoginAlunosList() {


        $conn = bd_connect_livel();

        if (!$conn) {
            $err_msg = 'N&atilde;o foi poss&iacute;vel estabelecer conex&atilde;o.';

        } else {

            $arr_alunos = array();

            $str_sql = "SELECT
                c001_alunos.c001_id_aluno_lo AS ID,
                c001_alunos.c001_nome_completo AS Nome
                FROM
                c001_alunos
                WHERE
                c001_alunos.c001_id_aluno_lo > 0
                ORDER BY
                c001_alunos.c001_id_aluno_lo ASC
            ";

            $rs_alunos = mysqli_query($conn, $str_sql);	   

            while($r = mysqli_fetch_assoc($rs_alunos)) {
                $arr_alunos[] = $r;
            }                                     
        }

        //$arr_result = array('Registros'=>count($arr_alunos),'PerfilMain'=>$arr_alunos, 'Erro'=>$err_msg);

        return $arr_alunos;
    }
?>