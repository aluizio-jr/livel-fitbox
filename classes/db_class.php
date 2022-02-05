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

	function nextID($tabela, $campo) {
		$conn = bd_connect_livel();
		if ($conn) {
			$str_sql = "SELECT MAX(" . $campo . ") AS last_id FROM " . $tabela;

			$rs_id = mysqli_query($conn, $str_sql);	   
			$num_id = mysqli_num_rows($rs_id);    

			if ($num_id > 0) {
				while($r = mysqli_fetch_assoc($rs_id)) {
					$lastID = $r['last_id'];
					$lastID++;
				}                         

			} else {
				$lastID = 1;
			}
			
			return $lastID;
			
		} else {
			return false;

		}
	}

	function queryBuscaValor($tabela, $campoRet, $campoBusca, $valorBusca, $join = '') {
		try {
			$conn = bd_connect_livel();
			if (!$conn) throw new Exception('Nao foi possivel conectar o banco de dados.');

			$str_sql = "SELECT " . $campoRet . " FROM " . $tabela . " ";
			$str_sql .= $join;
			$str_sql .= " WHERE " . $campoBusca . " = '" . $valorBusca . "' LIMIT 1";

			$rs = mysqli_query($conn, $str_sql);	   
			$num_rs = mysqli_num_rows($rs);

			if (!$num_rs > 0) throw new Exception('Query: ' . $str_sql);

			while($r = mysqli_fetch_assoc($rs)) {
				$retRs = $r[$campoRet];
			}                         

			return ["retFn" => true, "retRs" => $retRs];

		} catch(Exception $e) {
			return ["retFn" => false, "retRs" => $e->getMessage()];
		}
	}

?>