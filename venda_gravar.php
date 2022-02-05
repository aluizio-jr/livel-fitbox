<?php
    require_once "classes/db_class.php"; 
    require_once "classes/functions.php";
    require_once "classes/post_data.php";

    function validaItens($vendaItens) {
        $itens_count = count($vendaItens);
        if (!$itens_count) return ["validou"=>false, "error" => "Itens da venda nao informados."];

        for ($i=0; $i<$itens_count; $i++) {
            $item = $i++;
            if (!$vendaItens[$i]['id_plano_vigencia']) {
                return ["validou"=>false, "error" => "(Item: " . $item . ") ID do plano nao informado."];
            }
            
            $itemPlano = queryBuscaValor(
                'lo_plano_vigencias', 
                'lo_plano_vigencias.lo_id_plano', 
                'lo_plano_vigencias.lo_id_plano_vigencia', 
                $vendaItens[$i]['id_plano_vigencia']
            );

            if (!$itemPlano['retFn']) return ["validou"=>false, "error" => "(Item: " . $item . ") ID do plano nao encontrado. Result: " . $itemPlano['retRs']];
        }

        return ["validou"=>true, "error" => ""];
    }

    function validaVenda($vendaData) {
        $venda_count = count($vendaData);
        if (!$venda_count) return ["validou"=>false, "error" => "Dados da venda nao informados."];
        if (!$vendaData['id_cliente']) return ["validou"=>false, "error" => "Cliente nao informado."];
        if (!$vendaData['id_venda_tipo']) return ["validou"=>false, "error" => "Tipo de venda nao informado."];
        if ($vendaData['id_venda_tipo'] == 3 && !$vendaData['id_venda_renovacao']) return ["validou"=>false, "error" => "ID da venda renovada nao informado."];

        return ["validou"=>true, "error" => ""];
    }

    function gravarVenda($vendaData) {
        try {
            $validaVenda = validaVenda($vendaData);
            if (!$validaVenda['validou']) throw new Exception($validaVenda['error']);

            $validaItens = validaItens($vendaData['itens']);
            if (!$validaItens['validou']) throw new Exception($validaItens['error']);

            $itens_count = count($vendaPost['itens']);
            $parcelas_count = count($vendaPost['parcelas']);
            
            
            if (!$itens_count) throw new Exception('Itens da venda nao informados.');
            if (!$parcelas_count) throw new Exception('Parcelas da venda nao informadas.');

        } catch(Exception $e) {
            http_response_code(400);
            return ["error" => $e->getMessage()];
        }
    }

    $vendaPost = getPost();
    $retVenda = gravarVenda($vendaPost);
    echo json_encode($retVenda, JSON_UNESCAPED_SLASHES|JSON_NUMERIC_CHECK);

    

