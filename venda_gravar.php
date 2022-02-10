<?php
    require_once "classes/db_class.php"; 
    require_once "classes/functions.php";
    require_once "classes/post_data.php";
    require_once "venda_gravar_valida.php";
    require_once "venda_gravar_main.php";
    require_once "venda_gravar_itens.php";

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

//GRAVA VENDA MAIN
            $retVendaMain = vendaGravarMain($vendaData);
            if (!$retVendaMain['idVenda'])  throw new Exception($retVendaMain['error']);

//GRAVA VENDA ITENS
            $retVendaItens = vendaGravarItens($retVendaMain['idVenda'], $vendaData['itens']);
            if (!$retVendaItens['vendaItens'])  throw new Exception($retVendaItens['error']);

//GRAVA VENDA PARCELAS

            mysqli_commit($conn);
            return ["validou" => true, "error" => false];

        } catch(Exception $e) {
            if ($beginTrans) mysqli_rollback($conn);

            http_response_code(400);
            return ["error" => $e->getMessage()];
        }
    }

    $vendaPost = getPost();
    $retVenda = gravarVenda($vendaPost);
    echo json_encode($retVenda, JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);

    

