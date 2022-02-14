<?php

  require_once "classes/functions.php";
  require_once "classes/db_class.php";
  require_once "classes/asaas/asaas_clientes.php";

  $conn = bd_connect_livel();
  $retCliente = asaasCienteGravar('6', $conn, 0);

  echo json_encode($retCliente);