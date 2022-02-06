<?php
    require_once "classes/db_class.php"; 
    require_once "classes/functions.php";
    require_once "classes/post_data.php";
    require_once "venda_valida.php";

    function gravarVenda($vendaData) {
        try {
            $validaVenda = validaVenda($vendaData);
            if (!$validaVenda['validou']) throw new Exception($validaVenda['error']);

            $validaItens = validaVendaItens($vendaData['itens']);
            if (!$validaItens['validou']) throw new Exception($validaItens['error']);
            
            return ["validou" => true, "error": false]

            // $validaParcelas = validaParcelas($vendaData['[parcelas]']);
            // if (!$validaParcelas['validou']) throw new Exception($validaItens['error']);

            // $parcelas_count = count($vendaPost['parcelas']);
            // if (!$parcelas_count) throw new Exception('Parcelas da venda nao informadas.');

        } catch(Exception $e) {
            http_response_code(400);
            return ["error" => $e->getMessage()];
        }
    }

    $vendaPost = getPost();
    $retVenda = gravarVenda($vendaPost);
    echo json_encode($retVenda, JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);

    

