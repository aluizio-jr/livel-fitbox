<?php

require_once('../php-rest-api/autoload.php');
require_once('./configs.php');

$smsFacade = new SmsFacade($configs['alias'], $configs['password'], $configs['webServiceUrl']);

try {
    //Lista todas mensagens recebidas que ainda não foram consultadas. Retorna um objeto do tipo SmsReceivedResponse
    //que conterá as mensagens recebidas.
    $response = $smsFacade->listMessagesReceived();

    echo "Status: " . $response->getStatusCode() . " - " . $response->getStatusDescription();
    echo "<br />Detalhe: " . $response->getDetailCode() . " - " . $response->getDetailDescription();

    if ($response->hasMessages()) {
        $messages = $response->getReceivedMessages();
        foreach ($messages as $smsReceived) {
            echo "<br />Celular: " . $smsReceived->getMobile();
            echo "<br />Data de recebimento: " . $smsReceived->getDateReceived();
            echo "<br />Mensagem: " . $smsReceived->getBody();
            //Id da mensagem que originou a mensagem de resposta
            echo "<br />Id da mensagem de origem: " . $smsReceived->getSmsOriginId();
        }
    } else {
        echo "<br />Não foram encontradas mensagens recebidas.";
    }
} catch (Exception $ex) {
    echo "Falha ao listar as mensagens recebidas. Exceção: " . $ex->getMessage() . "<br />" . $ex->getTraceAsString();
}
