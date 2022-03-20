<?php
    require_once "classes/db_class.php"; 
    require_once "classes/functions.php";
    require_once "classes/post_data.php";
    require_once "classes/asaas/asaas_clientes.php";
    require_once "venda_gravar_cliente.php";
    require_once "venda_gravar_valida.php";
    require_once "venda_gravar_main.php";
    require_once "venda_gravar_itens.php";
    require_once "venda_gravar_parcelas.php";
    

    function gravarVenda($vendaData) {
        try {
            $beginTrans = false;

            $validaVenda = validaVenda($vendaData);
            if (!$validaVenda['validou']) throw new Exception($validaVenda['error']);

            $validaItens = validaVendaItens($vendaData['itens']);
            if (!$validaItens['validou']) throw new Exception($validaItens['error']);

            $validaParcelas = validaVendaParcelas($vendaData['parcelas']);
            if (!$validaParcelas['validou']) throw new Exception($validaParcelas['error']);

            $conn = bd_connect_livel();

            if (!$conn) throw new Exception("Nao foi possivel conectar ao banco de dados.");

            mysqli_begin_transaction($conn);
            $beginTrans = true;

//VALIDA O CLIENTE
            $retCliente = vendaGravarCliente($vendaData['cliente'], $conn);
            if (!$retCliente['idCliente']) throw new Exception($retCliente['error']);
            $idCliente = $retCliente['idCliente'];

//GRAVA VENDA MAIN
            $retVendaMain = vendaGravarMain($vendaData, $idCliente, $conn);
            if (!$retVendaMain['idVenda'])  throw new Exception($retVendaMain['error']);
            $idVenda = $retVendaMain['idVenda'];
            
//GRAVA VENDA ITENS
            $retVendaItens = vendaGravarItens($idVenda, $vendaData['itens'], $conn);
            if (!$retVendaItens['vendaItens'])  throw new Exception($retVendaItens['error']);

//GRAVA VENDA PARCELAS
            $retVendaParcelas = vendaGravarParcelas($idCliente, $idVenda, $vendaData['parcelas'], $conn);
            if (!$retVendaParcelas['vendaParcelas']) throw new Exception($retVendaParcelas['error']);

            mysqli_commit($conn);

            http_response_code(200);
            return ["validou" => true, "error" => false];

        } catch(Exception $e) {
            if ($beginTrans) mysqli_rollback($conn);

            http_response_code(400);
            return ["validou" => false, "error" => $e->getMessage()];
        }
    }

    $vendaPost = file_get_contents('php://input');
    $vendaPost = utf8_decode($vendaPost);
    $vendaData = json_decode($vendaPost, true); //getPost();

    $retVenda = gravarVenda($vendaData);
    echo json_encode($retVenda, JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);

    

