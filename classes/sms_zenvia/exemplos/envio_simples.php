<?php

function envia_sms_zenvia($sms_id, $destinario, $mensagem) {
    $smsFacade = new SmsFacade($configs['alias'], $configs['password'], $configs['webServiceUrl']);

    $sms = new Sms();
    $sms->setTo($destinario);
    $sms->setMsg($mensagem);
    
    if ($sms_id) {
        $sms->setId($sms_id);
    } else {
        $sms->setId(uniqid());
    }
    
    $sms->setCallbackOption(Sms::CALLBACK_NONE);

    $date = new DateTime();
    $date->setTimeZone(new DateTimeZone('America/Sao_Paulo'));
    $date->setDate(date('Y'), date('m'), date('d'));
    $date->setTime(date('H'), date('i'), 00);
    $schedule = $date->format("Y-m-d\TH:i:s");

    //Formato da data deve obedecer ao padrão descrito na ISO-8601. Exemplo "2015-12-31T09:00:00"
    $sms->setSchedule($schedule);

    try{
        //Envia a mensagem para o webservice e retorna um objeto do tipo SmsResponse com o status da mensagem enviada
        $response = $smsFacade->send($sms);
        $sms_enviado = (getStatusCode()!="00") ? false:true;
        $arr_result = array('Enviado'=>$sms_enviado,'StatusCode'=>$response->getStatusCode(),'StatusDesc'=>$response->getStatusDescription(), 'DetailCode'=>$response->getDetailCode(),'DetailDesc'=>$response->getDetailDescription());

    }
    catch(Exception $ex){
        $arr_result = array('Enviado'=>false,'StatusCode'=>'','StatusDesc'=>'Falha ao fazer o envio da mensagem. Exceção: '.$ex->getMessage().'<br />'.$ex->getTraceAsString(), 'DetailCode'=>'','DetailDesc'=>'');
        //$result = "Falha ao fazer o envio da mensagem. Exceção: ".$ex->getMessage()."<br />".$ex->getTraceAsString();
    }

    return $arr_result;
}
