<?php

	function bd_connect_livel() {

		$host="fitgroup.com.br";
		$user="fitgroup_livel";
		$pass="dhvtnc0809vps";
		$bd="fitgroup_livel";

		$cnn = mysqli_connect ($host, $user, $pass, $bd);

		if ($cnn) {
			$result = $cnn;
			$ret = mysqli_set_charset($cnn, "utf8");
			
		} else {
			$result = false; //array('cnn'=>false, 'erro_msg'=>mysqli_connect_error());

		}

		return $result;

	}

	function next_id_cv($tabela, $campo) {
		$conn = bd_connect_cv();
		if ($conn) {
			$str_sql = "SELECT MAX(" . $campo . ") AS last_id 
						 FROM " . $tabela;

			$rs_id = mysqli_query($conn, $str_sql);	   
			$num_id = mysqli_num_rows($rs_id);    

			if ($num_id > 0){
				while($r = mysqli_fetch_assoc($rs_id)) {
					$LastID = $r['last_id'];
					$LastID++;

				}                         

			} else {
				$LastID = 1;
			}
			
			return $LastID;
			
		} else {
			return false;

		}


	}
	function bd_connect_fitgroup() {

		$host="fitgroup.com.br";
		$user="fitgroup_fortal";
		$pass="fortal";
		$bd="fitgroup_gestor";

		$cnn = mysqli_connect ($host, $user, $pass, $bd);

		if ($cnn) {
			$result = $cnn;
			$ret = mysqli_set_charset($cnn, "utf8");
			
		} else {
			$result = false; //array('cnn'=>false, 'erro_msg'=>mysqli_connect_error());

		}

		return $result;

	}


	function bd_connect_academia($host) {
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

		$host=$host.":3307";
		$user="fitgroup";
		$pass="kmd011vsr012";
		$bd="fitgroup";

    	$cnn = mysqli_connect($host, $user, $pass, $bd);

		if ($cnn) {
			$ret = mysqli_set_charset($cnn, "utf8");
			$result = array('cnn'=>$cnn, 'erro_msg'=>false);
			//$ret = mysqli_query($cnn, "SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'";
			
		} else {
			$result = array('cnn'=>false, 'erro_msg'=>mysqli_connect_error());

		}

		return $result;

		//mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", $result);
		//return $result;

	}


	function academia_conecta($id_cliente) {

        $conn = bd_connect_fitgroup();

        if ($conn) {

            $str_sql = "SELECT dns_ip 
            FROM 
            cad_clientes_dns 
            WHERE 
            id_cliente = '" . $id_cliente . "' 
            LIMIT 1;";

            $rs_host = mysqli_query($conn, $str_sql);	   
            $num_host = mysqli_num_rows($rs_host);    

            if ($num_host > 0) {

                $r = mysqli_fetch_assoc($rs_host);


                $cliente_host = $r['dns_ip'];

                if($cliente_host) {
					$ret_cnn = bd_connect_academia($cliente_host);

					$cnn_acad = $ret_cnn['cnn'];
					$err_msg = $ret_cnn['erro_msg'];


				} else {
					$cnn_acad = false;
					$err_msg = 'academia host NOT FOUND'; 

                }


			} else {
				$cnn_acad = false;
				$err_msg = 'academia host NOT FOUND'; 
            }

		} else {
			$cnn_acad = false;
			$err_msg = 'bd_connect_fitgroup FAILED'; 

        }


		return array('host'=>$cliente_host, 'conexao'=>$cnn_acad, 'erro_msg'=>$err_msg); 

	}
	

?>