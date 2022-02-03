<?php

    function AlunoProfileDados($id_aluno) {


        $conn = bd_connect_livel();

        if (!$conn) {
            $err_msg = 'N&atilde;o foi poss&iacute;vel estabelecer conex&atilde;o.';

        } else {

            $arr_perfil = array();

            $str_sql = "SELECT
                c001_alunos.c001_id_aluno_lo AS AlunoID,
                c001_alunos.c001_id_cliente_box AS AlunoFitboxID,
                c001_alunos.c001_nome_completo AS AlunoNome,
                c001_alunos.c001_endereco_rua AS AlunoEnderecoRua,
                c001_alunos.c001_endereco_numero AS AlunoEnderecoNumero,
                c001_alunos.c001_endereco_complemento AS AlunoEndereComplemento,
                c001_alunos.c001_endereco_bairro AS AlunoEnderecoBairro,
                c001_alunos.c001_endereco_cep AS AlunoEnderecoCep,
                cs001a_municipios.cs001a_municipio_nome AS AlunoEnderecoCidade,
                cs001_estados.cs001_estado_sigla AS AlunoEnderecoUF,
                c001_alunos.c001_data_nascimento AS AlunoNascimento,
                c001_alunos.c001_aluno_sexo AS AlunoSexo,
                c001_alunos.c001_cpf AS AlunoCPF,
                c001_alunos.c001_email AS AlunoEmail,
                c001_alunos.c001_celular AS AlunoCelular,
                (CASE 
                    WHEN c001_alunos.c001_arquivo_foto IS NOT NULL 
                    THEN CONCAT('http://fitgroup.com.br/livel_fitbox/assets/' ,c001_alunos.c001_arquivo_foto) 
                    ELSE NULL 
                END) AS AlunoFoto

                FROM
                c001_alunos
                LEFT OUTER JOIN cs001a_municipios ON c001_alunos.cs001a_id_municipio = cs001a_municipios.cs001a_id_municipio
                LEFT OUTER JOIN cs001_estados ON cs001a_municipios.cs001_id_estado = cs001_estados.cs001_id_estado
                WHERE
                c001_alunos.c001_id_aluno_lo = " . $id_aluno;

            $rs_perfil = mysqli_query($conn, $str_sql);	   
            $num_perfil = mysqli_num_rows($rs_perfil);  

            while($r = mysqli_fetch_assoc($rs_perfil)) {
                $arr_perfil = $r;
            }                                     
        }

        $arr_result = array('Registros'=>$num_perfil,'Dados'=>$arr_perfil, 'Erro'=>$err_msg);

        return $arr_result;
    }
?>