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

	function nextID($tabela, $campo, $arrFilters = []) {
		$conn = bd_connect_livel();
		if ($conn) {
			$str_sql = "SELECT MAX(" . $campo . ") AS last_id FROM " . $tabela;

			foreach ($arrFilters as $campo => $valor) {
				$str_where .= $str_where ? " AND " : " WHERE ";
				$str_where .= $campo . " = '" . $valor . "'";
			}

			$str_sql .= $str_where;

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

	function queryInsert($tabela, $camposValores) {
		foreach ($camposValores as $campo => $valor) {
			$str_campos .= ($str_campos ? ", " : "") . $campo;

			$str_valores .= ($str_valores ? ", " : "");
			$str_valores .= ($valor ? "'" . $valor . "'" : "NULL");
		}

		$strInsert = "INSERT INTO " . $tabela . " (";
		$strInsert .= $str_campos;
		$strInsert .= ") VALUES (";
		$strInsert .= $str_valores;
		$strInsert .= ")";

		return $strInsert;
	}

	function queryUpdate($tabela, $camposValores) {
	
		foreach ($camposValores as $campo => $valor) {
			$strCampos .= ($strCampos ? ", " : "");
			$strCampos .=  $campo . " = " . ($valor ? "'" . $valor . "'" : "NULL");
		}

		$strUpdate = "UPDATE " . $tabela . " SET " . $strCampos;

		return $strUpdate;
	}

	function queryBuscaValor($tabela, $campoRet, $arrFilters, $join = '') {
		try {
			$conn = bd_connect_livel();
			if (!$conn) throw new Exception('Nao foi possivel conectar o banco de dados.');

			$str_sql = "SELECT " . $campoRet . " FROM " . $tabela . " ";
			$str_sql .= $join;

			foreach ($arrFilters as $campo => $valor) {
				$str_where .= $str_where ? " AND " : " WHERE ";
				$str_where .= $campo . " = '" . $valor . "'";
			}

			$str_sql .= $str_where;
			$str_sql .= " LIMIT 1";

			$rs = mysqli_query($conn, $str_sql);	   
			if (!$rs) throw new Exception(mysqli_error($conn));
			
			// $num_rs = mysqli_num_rows($rs);
			// if (!$num_rs > 0) throw new Exception('Query: ' . $str_sql);

			while($r = mysqli_fetch_assoc($rs)) {
				$retRs = $r[0];
			}                         

			return ["retFn" => true, "retValor" => $retRs, "error" => false];

		} catch(Exception $e) {
			return ["retFn" => false, "retValor" => false, "error" => $e->getMessage()];
		}
	}

?>