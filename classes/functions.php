<?php

	function vigenciaCalcula($vigenciaUnidade, $vigenciaQtde, $dataInicial = false) {
		
		if (!$dataInicial) $dataInicial = date('Y-m-d');

		$dd = $vigenciaUnidade == 1 ? $vigenciaQtde : $vigenciaQtde*30;
		$dayArg = ' + ' . $dd . ' days';
		return date('Y-m-d', strtotime($dataInicial. $dayArg)); 
	}

	function cardIsValid($cardNumber)
	{
		$number = substr($cardNumber, 0, -1);
		$doubles = [];

		for ($i = 0, $t = strlen($number); $i < $t; ++$i) {
			$doubles[] = substr($number, $i, 1) * ($i % 2 == 0? 2: 1);
		}

		$sum = 0;

		foreach ($doubles as $double) {
			for ($i = 0, $t = strlen($double); $i < $t; ++$i) {
				$sum += (int) substr($double, $i, 1);
			}
		}

		return substr($cardNumber, -1, 1) == (10-$sum%10)%10;
	}

	function DateDifDays ($dataIni, $dataFim = NULL) {
		if(!$dataFim) $dataFim = date('Y-m-d');
		
		$data1 = new DateTime($dataIni); 
		$data2 = new DateTime($dataFim); 

		$dDiff = $data2->diff($data1); 

		return $dDiff->format('%r%a');
	}

	function EncryptStringVB ($UserKey, $Text, $Action) {

	//EXEMPLO CHAMADA
	//EncryptString("KEY", texto_normal, ENCRYPT)
	//EncryptString("KEY", texto cripto, DECRYPT)
		
		//Obtem os caracteres da chave do usuário
		//define o comprimento da chave do usuario usada na criptografia
		$n = strLen($UserKey);

		
		//preenche o array com caracteres asc
		for ($i = 0; $i < $n; $i++) {
			$UserKeyASCIIS[$i] = ord(substr($UserKey, $i, 1));
		}
			
		//preenche o array com caracteres asc
		for ($i = 0; $i < strlen($Text); $i++) {
			$TEXTAsciis[$i] = ord(substr($Text, $i, 1));
		}
		
		print_r($TEXTAsciis);

		If ($Action == 'ENCRYPT') {
			for ($i = 0; $i < strLen($Text); $i++) {
				$z = $j++;
				$j = ($z >= $n ? 1 : $j++);
				$temp = $TEXTAsciis[$i] + $UserKeyASCIIS[$j];
				If ($temp > 255) {
					$temp = $temp - 255;
				}

				$rtn = $rtn + chr($temp);
			}

		} else  if ($Action == 'DECRYPT') {
			for ($i = 0; $i < strLen($Text); $i++) {
				$z = $j++;
				$j = ($z >= $n ? 1 : $j++);
				$temp = $TEXTAsciis[$i] - $UserKeyASCIIS[$j];
				
				If ($temp < 0) {
					$temp = $temp + 255;
				}	
				echo mb_convert_encoding($temp . ' / ' .  chr((int) $temp), 'UTF-8')  . "<br>";
				$rtn .= $rtn + chr((int) 	$temp);
			}
		}
		
		//Retorna o texto
		return $rtn;
	}


	function TempoToMilisegundos ($time) {
		
		$arr_time = explode(':', $time);
		$mili_sec = ((int) $arr_time[0]*3600)*1000;
		$mili_sec += ((int) $arr_time[1]*60)*1000;
		$mili_sec += (int) $arr_time[2]*1000;

		return $mili_sec;
	}

	function gerarCodigoValidacao($qtyCaraceters = 8) {
		//Letras minúsculas embaralhadas
		//$smallLetters = str_shuffle('abcdefghijklmnopqrstuvwxyz');

		//Letras maiúsculas embaralhadas
		$capitalLetters = str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ');

		//Números aleatórios
		$numbers = (((date('Ymd') / 12) * 24) + mt_rand(800, 9999));
		$numbers .= 1234567890;

		//Caracteres Especiais
		//$specialCharacters = str_shuffle('!@#$%*-');

		//Junta tudo
		//$characters = $capitalLetters.$smallLetters.$numbers.$specialCharacters;
		$characters = $capitalLetters.$numbers;

		//Embaralha e pega apenas a quantidade de caracteres informada no parâmetro
		$password = substr(str_shuffle($characters), 0, $qtyCaraceters);

		//Retorna a senha
		return $password;
	}


	function validaEmail($email){
		//verifica se e-mail esta no formato correto de escrita
		if (!preg_match('/^([a-zA-Z0-9.-_])*([@])([a-z0-9]).([a-z]{2,3})/',$email)){
			//$mensagem='E-mail Inv&aacute;lido!';
			return false;
		}
		else{
			//Valida o dominio
			$dominio=explode('@',$email);
			if(!checkdnsrr($dominio[1],'A')){
				//$mensagem='E-mail Inv&aacute;lido!';
				return false;
			}
			else{return true;} // Retorno true para indicar que o e-mail é valido
		}
	}

	function envia_email($destinatario, $subject, $msg, $remetente_nome, $remetente_mail) {

		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
		$headers .= 'From:' . $remetente_nome . '<' . $remetente_mail . '>';

		//return mail($destinatario, $subject, $msg, $headers); 
		//$mai_sent = mail($destinatario, $subject, $msg, $headers); 
		$ret_mail = mail($destinatario, $subject, $msg, $headers);
/*		
		if (!$success) {
			$errorMessage = error_get_last()['message'];
		} else {
			$errorMessage = $success;
		}
*/
		return $ret_mail;
	}

	function validaCelular($celular){
		if (preg_match('/^(?:\(?([1-9][0-9])\)?\s?)?(?:((?:9\d|[2-9])\d{3})\-?(\d{4}))$/', $celular)) {
			return trim(str_replace('/', '', str_replace(' ', '', str_replace('-', '', str_replace(')', '', str_replace('(', '', $celular))))));
		} else {
			return false;
		}
	}
		
	function httpGet($url) {
		$cURL = curl_init();  
	
		curl_setopt($cURL,CURLOPT_URL,$url);
		curl_setopt($cURL,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($cURL, CURLOPT_HTTPGET, true);

		curl_setopt($cURL, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Accept: application/json'
		));
	
		$output = curl_exec($cURL);
		curl_close($cURL);

		return $output;
		
		
	}	  

	function convert_to_utf8_recursively($dat){
		if( is_string($dat) ){
		return mb_convert_encoding($dat, 'UTF-8', 'UTF-8');
		}
		elseif( is_array($dat) ){
		$ret = array();
		foreach($dat as $i => $d){
			$ret[$i] = convert_to_utf8_recursively($d);
		}
		return $ret;
		}
		else{
		return $dat;
		}
	}

?>