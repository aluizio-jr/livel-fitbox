<?php

  require_once "classes/functions.php";
  require_once "classes/db_class.php";
  require_once "classes/asaas/asaas_clientes.php";

  $vendaPost = file_get_contents('php://input');
  $vendaPost = utf8_decode($vendaPost);
  $vendaPost = json_decode($vendaPost, true); //getPost();
  print_r($vendaPost);
  // $jsonStr = '{"ALUNO_CADASTRO":{"object":"customer","id":"cus_000028985404","dateCreated":"2022-02-14","name":"ARICELIO
  //   JUNIOR","email":"aricelio.jr@gmail.com","company":null,"phone":"31973065778","mobilePhone":"31973065778","address":"Rua
  //   Antonio Orlindo de Castro","addressNumber":"441","complement":"708","province":"Sao Joao Batista (Venda
  //   Nova)","postalCode":"31515290","cpfCnpj":"08344835640","personType":"FISICA","deleted":false,"additionalEmails":null,"externalReference":"6","notificationDisabled":true,"observations":null,"city":10072,"state":"MG","country":"Brasil","foreignCustomer":false}}';
  
  // $arrJson = json_encode($jsonStr);
  // print_r($arrJson);

  // $conn = bd_connect_livel();
  // $retCliente = asaasCienteGravar('6', $conn, 0);

  // echo json_encode($retCliente);