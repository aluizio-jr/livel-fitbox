<?php

    function acessoAdd($id_aluno_gestor) {
        
        $conn = bd_connect_cv();

        if ($conn) {
            $str_sql = " INSERT INTO cv001d_alunos_acessos (
                c001_id_aluno_gestor,
                cv001d_data_hora
                ) VALUES ("
                . $id_aluno_gestor 
                . ",'" . date('Y-m-d') . " " . date('H:n:s') . "')";

            $num_upd = mysqli_query($conn,$str_sql);

            $result = array('Registros'=>$num_upd);
        
        } else {
            $result = array('Registros'=>0);
        }

        return $result;
    }

?>