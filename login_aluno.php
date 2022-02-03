<?php
    function AlunoLogin($celular) {

        $num_login = 0;
        $arr_login = array();

        $conn = bd_connect_livel();

        if (!$conn) {
            $err_msg = 'N&atilde;o foi poss&iacute;vel estabelecer conex&atilde;o.';

    
        } else {

            $str_sql = "SELECT c001_alunos.c001_id_aluno_lo AS id_aluno,
                c001_validacao_codigo AS celular_validacao_codigo,
                c001_validacao_data AS celular_validacao_data,
                c001_aluno_ativo AS aluno_ativo
                FROM c001_alunos 
                WHERE c001_alunos.c001_celular = '" . $celular . "' 
                LIMIT 1;";

            $rs_login = mysqli_query($conn, $str_sql);	   
            $num_login = mysqli_num_rows($rs_login);    
            
            if ($num_login > 0){
                
                while($r = mysqli_fetch_assoc($rs_login)) {
                    $id_aluno = $r['id_aluno'];
                    $aluno_ativo = $r['aluno_ativo'];
                    $celular_validacao_data = $r['celular_validacao_data'];

                }                         
                
                $arr_login = array('AlunoID' => $id_aluno,
                                    'CelularValidado' => ($celular_validacao_data) ? 1 : 0,
                                    'AlunoAtivo' => $aluno_ativo
                                );

            } else {
                $arr_login = array('AlunoID' => NULL,
                                    'CelularValidado' => 0,
                                    'AlunoAtivo' => 0
                                );                
            }

        }

        return $arr_login;
    }
?>