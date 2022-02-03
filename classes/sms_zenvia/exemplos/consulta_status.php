<?php

require_once('../php-rest-api/autoload.php');
require_once('./configs.php');

$smsFacade = new SmsFacade($configs['alias'], $configs['password'], $configs['webServiceUrl']);

//Id da mensagem que deverá ser cancelada
$id = "53d67682504c8";

try {
    $response = $smsFacade->getStatus($id);
    //Código e descrição do status atual da mensagem
    echo "Status: " . $response->getStatusCode() . " - " . $response->getStatusDescription();
    //Código e descrição do detalhe do status atual da mensagem
    echo "<br />Detalhe: " . $response->getDetailCode() . " - " . $response->getDetailDescription();
    if ($response->getStatusCode() == "00") {
        //Id da mensagem
        echo "<br />Id: " . $response->getId();
        //Data de recebimento da mensagem no celular
        echo "<br />Recebido em: " . $response->getReceived();
    } else {
        echo "<br />Status da mensagem não pôde ser consultado.";
    }
} catch (Exception $ex) {
    echo "Falha ao fazer consulta de status da mensagem. Exceção: " . $ex->getMessage() . "<br />" . $ex->getTraceAsString();
}
