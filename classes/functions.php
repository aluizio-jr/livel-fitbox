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

	function envia_sms($celular, $msg, $sms_id, $remetente) {
		//echo $celular . " | " . $msg . " | " . $sms_id . " | " . $remetente;

		$celular_sms = validaCelular($celular);

		if ($celular_sms) {
			$celular_sms = "55" . $celular_sms;

			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_URL, "https://api-rest.zenvia.com/services/send-sms");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			
			curl_setopt($ch, CURLOPT_POST, TRUE);
			
			curl_setopt($ch, CURLOPT_POSTFIELDS, "{
			  \"sendSmsRequest\": {
				\"from\": \"$remetente\",
				\"to\": \"" . $celular_sms . "\",
				\"schedule\": \"" . Date('Y-m-d') . "T" . Date('H:i:s') . "\",
				\"msg\": \"$msg\",
				\"callbackOption\": \"NONE\",
				\"id\": \"$sms_id\",
				\"aggregateId\": \"1111\",
				\"flashSms\": false
			  }
			}");
			
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			  "Content-Type: application/json",
			  "Authorization: Basic " . base64_encode('aluiziofra.smsonline:I0R4dM9kNC') . "/",
			  "Accept: application/json"
			));
			
			$response = curl_exec($ch);
			curl_close($ch);

			
			$sms_status = json_decode($response, true);
			$sms_enviado = ($sms_status['sendSmsResponse']['statusCode']=='00')? true:false;
			$sms_envio = array('Enviado'=>$sms_enviado,'Retorno'=>$sms_status['sendSmsResponse']['statusDescription']);
			//$sms_envio = array('Response'=>$response,'Status'=>$sms_status);
				
			
					
/*
			$url = "http://209.133.205.2/shortcode/api.ashx?action=sendsms&lgn=31971636968&pwd=505320";
			$url.= "&msg=" . $msg;
			$url.= "&numbers=" . $celular;

			$sms = httpGet($url);
			$json = json_decode($sms, true);

			$sms_envio = array('Enviado'=>$json['status'],'Retorno'=>$json['msg']);

*/
		} else {
			$sms_envio = array('Enviado'=>false,'Retorno'=>'Celular inválido.');

		}

		return $sms_envio;
	}

    function AcessoAdd($id_aluno_gestor) {
        
        $conn = bd_connect_cv();

        if ($conn) {
            $str_sql = " INSERT INTO cv001d_alunos_acessos (
                c001_id_aluno_gestor,
                cv001d_data_hora
                ) VALUES ("
                . $id_aluno_gestor 
                . ",'" . date('Y-m-d') . " " . date('H:i:s') . "')";

            $num_upd = mysqli_query($conn,$str_sql);

            $result = array('Registros'=>$num_upd);
        
        } else {
            $result = array('Registros'=>0);
        }

        return $result;
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

	function proper_case($frase) {
		$words = explode(" ", $frase);
		for ($i=0; $i<count($words); $i++) {
		$s = strtolower($words[$i]);
		$s = substr_replace($s, strtoupper(substr($s, 0, 1)), 0, 1);
		$result .= "$s ";
		}
		$frase = trim($result);	
		
		return $frase;	
	}

	function nome_menor($nome) {
		$nombres = explode(" ", $nome);
		
		$novo_nome = $nombres[0] . " " . $nombres[1];
		
		if(strlen($nombres[1]) < 4 && $nombres[2]) $novo_nome.= " " . $nombres[2];

		return $novo_nome;

	
	}
	
			
	function converte_data_show($data) {
	
		if(!$data):
			return "";
		
		else:
			$data_show=substr($data,8,2) . "/" . substr($data,5,2) . "/" . substr($data,0,4);
			return $data_show;
			
		endif;
			
	}
	
	
	function converte_data_grava($data) {
		if (!$data):
			return "";
		
		else:
			$return =  $data['year'] . "-" .  zero_left(2,$data['mon']) . "-" . zero_left(2,$data['day']) ;	
		endif;
	}
	


	function converte_data($tipo, $data) {
		if ($tipo=="data"):
			$date = substr($data,6,4) . "-" . substr($data,3,2) . "-" . substr($data,0,2);
			return $date;
			
		elseif ($tipo=="hora"):
			$hora = substr($data,0,2) . ":" . substr($data,3,2) . ":00";
			return $hora;
		
		endif;
	}			


	function days_diff($date_ini, $date_end, $round = 0, $format_ini, $format_end) { 
		if ($format_ini == 1):
			$date_ini = strtotime(substr($date_ini,5,2) . "/" . substr($date_ini,8,2) . "/" . substr($date_ini,0,4));
		elseif ($format_ini == 0):
			$date_ini = strtotime($date_ini); 
		endif;
		
		if ($format_end == 1):
			$date_end = strtotime(substr($date_end,5,2) . "/" . substr($date_end,8,2) . "/" . substr($date_end,0,4));
		elseif($format_end == 0):	
			$date_end = strtotime($date_end); 
		endif;
		
		$date_diff = ($date_end - $date_ini) / 86400; 
	
		if($round != 0): 
			return floor($date_diff); 
		else:
			return $date_diff; 
		endif;
	} 
	

	function tempo_to_sec ($hms) {
		list($h, $m, $s) = explode (":", $hms);
		$seconds = 0;
		$seconds += (intval($h) * 3600);
		$seconds += (intval($m) * 60);
		$seconds += (intval($s));
		return $seconds;
	}


	function tempo_to_min ($hms) {
		list($h, $m, $s) = explode (":", $hms);
		$seconds = 0;
		$seconds += (intval($h) * 60);
		$seconds += (intval($m));
		$seconds += (intval($s) / 60);
		return $seconds;
	}
/*
	function date_add($data_ini, $valor, $tipo) {
		
		$data_ini = date("Y-m-d",strtotime($data_ini));
		
		$dd = substr($data_ini,8,2);
		$mm = substr($data_ini,5,2);
		$yy = substr($data_ini,0,4);
		
		if($tipo=="dia"):
			$nova_data  = date("Y-m-d",mktime (0, 0, 0, $mm  , $dd + $valor, $yy));
		elseif($tipo=="mes"):
			$nova_data  = date("Y-m-d",mktime (0, 0, 0, $mm + $valor , $dd, $yy));
		elseif($tipo=="ano"):
			$nova_data  = date("Y-m-d",mktime (0, 0, 0, $mm, $dd, $yy + $valor));
		endif;
					
		return $nova_data;
		
	}
	
*/
//CALCULA A DIFEREN�A ENTRE DATAS (Retorna dia, horas ou minutos, exatos ou arrendodados)
function diferenca_data($data1, $data2="",$tipo=""){
	//data1 � a menor
	if($data2==""){
	$data2 = date("d/m/Y H:i");
	}
	
	if($tipo==""){
	$tipo = "h";
	}
	
	for($i=1;$i<=2;$i++){
	${"dia".$i} = substr(${"data".$i},0,2);
	${"mes".$i} = substr(${"data".$i},3,2);
	${"ano".$i} = substr(${"data".$i},6,4);
	${"horas".$i} = substr(${"data".$i},11,2);
	${"minutos".$i} = substr(${"data".$i},14,2);
	}
	
	$segundos = mktime($horas2,$minutos2,0,$mes2,$dia2,$ano2) - mktime($horas1,$minutos1,0,$mes1,$dia1,$ano1);
	
	switch($tipo){
	
	 case "m": $difere = $segundos/60;    break;
	 case "H": $difere = $segundos/3600;    break;
	 case "h": $difere = round($segundos/3600);    break;
	 case "D": $difere = $segundos/86400;    break;
	 case "d": $difere = round($segundos/86400);    break;
	}
	
	return $difere;

}



//RETORNA DIA DA SEMANA

function dia_Semana($data) {

	$rs = strftime('%w',strtotime($data));
	
	switch($rs) {
		case "0":
			$s="Domingo";
			break;
		case "1":
			$s="Segunda";
			break;
		case "2":
			$s="Ter�a";
			break;
		case "3":
			$s="Quarta";
			break;
		case "4":
			$s="Quinta";
			break;
		case "5":
			$s="Sexta";
			break;
		case "6":
			$s="S�bado";
			break;
	}
	return $s;
}


	function nome_dia_vb($num_dia) {

		switch ($num_dia) {

			case 1;	
				return "Domingo";
				break;
			
			case 2;
				return "Segunda";
				break;
			
			case 3;	
				return "Ter�a";
				break;

			case 4;	
				return "Quarta";
				break;
				
			case 5;	
				return "Quinta";
				break;
				
			case 6;	
				return "Sexta";
				break;
				
			case 7;	
				return "S�bado";
				break;
				
		}
	
	}

function nome_dia_php($num_dia) {

	
	switch($num_dia) {
		case "0":
			$s="Domingo";
			break;
		case "1":
			$s="Segunda";
			break;
		case "2":
			$s="Ter�a";
			break;
		case "3":
			$s="Quarta";
			break;
		case "4":
			$s="Quinta";
			break;
		case "5":
			$s="Sexta";
			break;
		case "6":
			$s="S�bado";
			break;
	}
	return $s;
}


	function nome($extensao)
	{
		global $config;
	
		// Gera um nome �nico para a imagem
		$temp = substr(md5(uniqid(time())), 0, 10);
		$imagem_nome = $temp . "." . $extensao;
		
		// Verifica se o arquivo j� existe, caso positivo, chama essa fun��o novamente
		if(file_exists($config["diretorio"] . $imagem_nome))
		{
			$imagem_nome = nome($extensao);
		}
	
		return $imagem_nome;
	}
  
   

  function add_flash($arquivo, $alinhamento, $hspace) {
  
		$flash_path = getenv("DOCUMENT_ROOT") . "/" . $_SESSION["user"]["subdominio"] . "/custom/" . $arquivo;
		
		if (file_exists($flash_path)):
  
			return "<object align=\"" . $alinhamento . "\" width=\"100\" height=\"100\" hspace=\"" . $hspace . "\" classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0\">
				  <param name=\"movie\" value=\"custom/" . $arquivo . "\" />
				  <param name=\"quality\" value=\"high\" />
				  <embed src=\"custom/" . $arquivo . "\" quality=\"high\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\" width=\"100\" height=\"100\" align=\"" . $alinhamento . "\" hspace=\"" . $hspace . "\"></embed>
				</object>";
   
   		else:
			return "&nbsp;";

		endif;
   }

   function add_imagem($arquivo, $alinhamento="", $hspace=0, $folder="custom") {
   
		$imagem_path = getenv("DOCUMENT_ROOT") . "/" . $_SESSION["user"]["subdominio"] . "/" . $folder . "/" . $arquivo;

		if (file_exists($imagem_path)):
  
			$img = "<img src=\"../" . $_SESSION["user"]["subdominio"] . "/" . $folder . "/" . $arquivo . "\"";
			
			if($alinhamento) $img.=" align=" . $alinhamento;
			//if($hspace) $img.=" hspace=\"" . $hspace . "\" vspace=\"" . $hspace . "\"";
			if($hspace) $img.=" style=\"margin:" . $hspace . "px;\" ";
			$img .= " border=0>";
			return $img;
   
   		else:
			return "&nbsp;";

		endif;

   
   }
   
   function add_video($video_url) {
   		
		return "<object width=\"229\" height=\"229\">
				<param name=\"movie\" value=\"" . $video_url . "\"></param>
				<param name=\"allowFullScreen\" value=\"true\"></param>
				<param name=\"allowscriptaccess\" value=\"always\"></param>
				<embed src=\"" . $video_url . "\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"229\" height=\"229\"></embed>
				</object>";
				
   }
   
   function add_podcast($arquivo) {
		
		return "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0\" width=\"165\" height=\"37\" id=\"niftyPlayer1\" align=\"center\">
		 <param name=movie value=\"" . $_SESSION["user"]["subdominio"] . "/custom/niftyplayer.swf?file=" . $_SESSION["user"]["subdominio"] . "/custom/" . $arquivo . "&amp;as=0\">
		 <param name=quality value=high>
		 <param name=bgcolor value=#FFFFFF>
		 <embed src=\"" . $_SESSION["user"]["subdominio"] . "/custom/niftyplayer.swf?file=" . $_SESSION["user"]["subdominio"] . "/custom/" . $arquivo . "&amp;as=0\" quality=high bgcolor=#FFFFFF width=\"165\" height=\"37\" name=\"niftyPlayer1\" align=\"center\" type=\"application/x-shockwave-flash\" swLiveConnect=\"true\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\">
		</embed>
		</object>";
   
   }

	
   
	function resize_jpg($imagem_path, $new_side){
	
		//$imagem_path = getenv("DOCUMENT_ROOT") . "/" . $_SESSION["user"]["subdominio"] . "/custom/" . $arquivo;
		
		if (file_exists($imagem_path)):
	
			$imagedata = getimagesize($imagem_path);
			$w = $imagedata[0];
			$h = $imagedata[1];
			
			if ($h > $w) {
				$new_w = ($new_side / $h) * $w;
				$new_h = $new_side;	
			} else {
				$new_h = ($new_side / $w) * $h;
				$new_w = $new_side;
			}
			
			return "width=" . $new_w . " height=" . $new_h;
	
		else:	   
			return "";
		endif;
	
	}	


	function thumb_prop_size($imagem_path, $new_side){
	
		$imagedata = getimagesize($imagem_path);
		
		$w = $imagedata[0];
		$h = $imagedata[1];
		
		$img_wh = array();
		
		if ($h > $w) {
			$img_wh['w'] = ($new_side / $h) * $w;
			$img_wh['h'] = $new_side;	
		} else {
			$img_wh['h'] = ($new_side / $w) * $h;
			$img_wh['w'] = $new_side;
		}
		
		return $img_wh;
	
	}	

	//error handler function
function customError($errno, $errstr) {
  return "Erro=". $errstr;

}

//set error handler
set_error_handler("customError");



?>