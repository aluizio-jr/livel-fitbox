<?php
    function academia_dados($id_cliente) {

        $conn = bd_connect_fitgroup();

        if ($conn) {
           

            $str_sql = "SELECT " .
            "mobile_clientes_config.cliente_nome_cifra, " .
            "mobile_clientes_config.cliente_email_contato, " .
            "mobile_clientes_config.cliente_celular_contato " .
            "FROM " .
            "mobile_clientes_config " .
            "WHERE " .
            "mobile_clientes_config.id_cliente = '" . $id_cliente . "' " .
            "AND mobile_clientes_config.mobile_ativo = 1";

            $rs_info = mysql_query($str_sql, $conn);	   
            $num_info = mysql_num_rows($rs_info); 

            if ($num_info > 0) {
                $arr_result = mysql_fetch_assoc($rs_info);
                /*
                while($r = mysql_fetch_assoc($rs_info)) {
                    $arr_result[] = $r;
                }                         
                */
            }
$rs_teste = mysql_query(str_sql, conn);
num_info = mysql_num_rows($rs_teste);



    function academia_aluno($id_aluno_gestor) {

        $id_cliente='';

        $conn = bd_connect_fitgroup();

        if ($conn) {

            $str_sql = "SELECT c001_alunos.id_cliente " .
            "FROM " .
            "c001_alunos " .
            "WHERE " .
            "c001_alunos.id_aluno_gestor = '" . $id_aluno_gestor . "' " .
            "LIMIT 1;";

            $rs_acad = mysql_query($str_sql, $conn);	   
            $num_acad = mysql_num_rows($rs_acad);    


            if ($num_acad > 0) {

                $r = mysql_fetch_assoc($rs_acad);
                $id_cliente = $r['id_cliente'];

            }

            
        }
        
        return $id_cliente;

    }


?>