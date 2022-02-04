<?php
    require_once "classes/db_class.php"; 
    require_once "classes/functions.php";
    require_once "classes/post_data.php";

    $vendaPost = getPost();

    $retVenda = gravarVenda($vendaPost);
    echo $retVenda;

    function gravarVenda($vendaData) {
        try {
            $venda_count = count($vendaPost);
            $itens_count = count($vendaPost['itens']);
            $parcelas_count = count($vendaPost['parcelas']);
            throw new Exception('foo');

        } catch(Exception $e) {
            http_response_code(400);
            return json_encode(["error" => $e->getMessage()], JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);
        }
    }

