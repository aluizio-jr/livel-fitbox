<?php

    $celular = $_GET['SMS_Celular'];
    $msg = $_GET['SMS_Msg'];
    $sms_id = $_GET['SMS_ID'];
    $remetente = $_GET['SMS_Remetente'];

    echo envia_sms($celular, $msg, $sms_id, $remetente);

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
            $sms_envio = ($sms_status['sendSmsResponse']['statusCode']=='00')? "1":"0";
            //$sms_envio = array('Enviado'=>$sms_enviado,'Retorno'=>$sms_status['sendSmsResponse']['statusDescription']);

        } else {
            $sms_envio = "0";

        }

        return $sms_envio;
    }

?>