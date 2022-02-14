<?php

    function validarCartao($cartao, $cvc=false){
        $cartao = preg_replace("/[^0-9]/", "", $cartao);
        if($cvc) $cvc = preg_replace("/[^0-9]/", "", $cvc);

        $cartoes = array(
                'visa'		 => array('len' => array(13,16),    'cvc' => 3),
                'mastercard' => array('len' => array(16),       'cvc' => 3),
                'diners'	 => array('len' => array(14,16),    'cvc' => 3),
                'elo'		 => array('len' => array(16),       'cvc' => 3),
                'amex'	 	 => array('len' => array(15),       'cvc' => 4),
                'discover'	 => array('len' => array(16),       'cvc' => 4),
                'aura'		 => array('len' => array(16),       'cvc' => 3),
                'jcb'		 => array('len' => array(16),       'cvc' => 3),
                'hipercard'  => array('len' => array(13,16,19), 'cvc' => 3),
        );

        
        switch($cartao){
            case (bool) preg_match('/^(636368|438935|504175|451416|636297)/', $cartao) :
                $bandeira = 'elo';			
            break;

            case (bool) preg_match('/^(606282)/', $cartao) :
                $bandeira = 'hipercard';			
            break;

            case (bool) preg_match('/^(5067|4576|4011)/', $cartao) :
                $bandeira = 'elo';			
            break;

            case (bool) preg_match('/^(3841)/', $cartao) :
                $bandeira = 'hipercard';			
            break;

            case (bool) preg_match('/^(6011)/', $cartao) :
                $bandeira = 'discover';			
            break;

            case (bool) preg_match('/^(622)/', $cartao) :
                $bandeira = 'discover';			
            break;

            case (bool) preg_match('/^(301|305)/', $cartao) :
                $bandeira = 'diners';			
            break;

            case (bool) preg_match('/^(34|37)/', $cartao) :
                $bandeira = 'amex';			
            break;

            case (bool) preg_match('/^(36,38)/', $cartao) :
                $bandeira = 'diners';			
            break;

            case (bool) preg_match('/^(64,65)/', $cartao) :
                $bandeira = 'discover';			
            break;

            case (bool) preg_match('/^(50)/', $cartao) :
                $bandeira = 'aura';			
            break;

            case (bool) preg_match('/^(35)/', $cartao) :
                $bandeira = 'jcb';			
            break;

            case (bool) preg_match('/^(60)/', $cartao) :
                $bandeira = 'hipercard';			
            break;

            case (bool) preg_match('/^(4)/', $cartao) :
                $bandeira = 'visa';			
            break;

            case (bool) preg_match('/^(5)/', $cartao) ||  (bool) preg_match('/^(2)/', $cartao) :
                $bandeira = 'mastercard';			
            break;
        }

        $dados_cartao = $cartoes[$bandeira];
        if(!is_array($dados_cartao)) return array(false, false, false);

        $valid     = true;
        $valid_cvc = false;

        if(!in_array(strlen($cartao), $dados_cartao['len'])) $valid = false;
        if($cvc AND strlen($cvc) <= $dados_cartao['cvc'] AND strlen($cvc) !=0) $valid_cvc = true;

        return array(
            'bandeira' => $bandeira, 
            'cartao_valido'=> $valid, 
            'cvc_valido'=> $valid_cvc
        );
    }


    function file_get_contents_utf8() {
        $content = file_get_contents('php://input');
            return mb_convert_encoding($content, 'UTF-8',
                mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
    }
        
    function convert_to_utf8_recursively($dat) {
        if(is_string($dat) ) {
            return mb_convert_encoding($dat, 'UTF-8', 'UTF-8');
        
        } else if(is_array($dat)) {
            $ret = array();
            foreach($dat as $i => $d){
            $ret[$i] = convert_to_utf8_recursively($d);
            }
            return $ret;
        }
        else {
            return $dat;
        }
    }

    function array_to_xml(array $arr, SimpleXMLElement $xml) {
        foreach ($arr as $k => $v) {
    
            $attrArr = array();
            $kArray = explode(' ',$k);
            $tag = array_shift($kArray);
    
            if (count($kArray) > 0) {
                foreach($kArray as $attrValue) {
                    $attrArr[] = explode('=',$attrValue);                   
                }
            }
    
            if (is_array($v)) {
                if (is_numeric($k)) {
                    array_to_xml($v, $xml);
                } else {
                    $child = $xml->addChild($tag);
                    if (isset($attrArr)) {
                        foreach($attrArr as $attrArrV) {
                            $child->addAttribute($attrArrV[0],$attrArrV[1]);
                        }
                    }                   
                    array_to_xml($v, $child);
                }
            } else {
                $child = $xml->addChild($tag, $v);
                if (isset($attrArr)) {
                    foreach($attrArr as $attrArrV) {
                        $child->addAttribute($attrArrV[0],$attrArrV[1]);
                    }
                }
            }               
        }
    
        return $xml;
    }
    
    function ImageBase64Create($name,$directory){
        define('UPLOAD_DIR', $directory);
        $img = base64_encode(file_get_contents( $_FILES[$name]["tmp_name"] ));
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $basename=uniqid();
        $file = UPLOAD_DIR . $basename . '.png';
        $success=file_put_contents($file, $data);
        print $success ? $file : 'Unable to save the file.';
        return $basename.'.png';
    }
                
    function EmailSend($destino, $assunto, $mensagem, $remetente = 'cobranca@fitgroup.com.br', $nome = 'FITGROUP ERP') {

        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: ' . $nome . '<' . $remetente . '>';
        //$headers .= "Bcc: $EmailPadrao\r\n";

        $ret_mail = mail($destino,$assunto,$mensagem, $headers); //mail($destino, $assunto, , $headers);
        if($ret_mail){
            $ret = "E-MAIL ENVIADO COM SUCESSO!";
        
        } else {
            $ret = "ERRO AO ENVIAR E-MAIL!";
    
        }

        return $ret;
    }

    function CharLeft($str, $len, $char) {
        $num_char = $len - strlen($str);
        $ret_str = $str;

        for ($i = 1; $i <= $num_char; $i++) {
            $ret_str  = $char . $ret_str;
        }

        return $ret_str;
    }
    