<?php
    require_once "classes/db_class.php"; 
    require_once "classes/functions.php";
    
    function validaVenda($vendaData) {
        $venda_count = count($vendaData);
        if (!$venda_count) return ["validou"=>false, "error" => "Dados da venda nao informados."];
        if (!$vendaData['id_cliente']) return ["validou"=>false, "error" => "Cliente nao informado."];
        if (!$vendaData['id_venda_tipo']) return ["validou"=>false, "error" => "Tipo de venda nao informado."];
        if ($vendaData['id_venda_tipo'] == 3 && !$vendaData['id_venda_renovacao']) return ["validou"=>false, "error" => "ID da venda renovada nao informado."];

        return ["validou"=>true, "error" => ""];
    }

    function validaVendaItens($vendaItens) {
        try {
            $itens_count = count($vendaItens);
            if (!$itens_count) throw new Exception("Itens da venda nao informados."); //return ["validou"=>false, "error" => "Itens da venda nao informados: " . $itens_count];
            
            for ($i=0; $i<$itens_count; $i++) {
                $item++;
                if (!$vendaItens[$i]['id_plano_vigencia']) {
                    throw new Exception("(Item: " . $item . ") ID do plano nao informado.");
                }

                $filters = [
                    "lo_id_plano_vigencia" => $vendaItens[$i]['id_plano_vigencia'],
                    "lo_id_produto_categoria" => 1
                ];

                //return ["validou"=>false, "filtros" => $filters];

                $itemPlano = queryBuscaValor(
                    'lo_plano_produtos', 
                    ' COUNT(*) ', 
                    $filters,
                    ' JOIN lo_plano_vigencias USING(lo_id_plano) '
                );

                if ($itemPlano['error']) {
                    throw new Exception("(Item: " . $item . ") Erro busca categoria Live:" . $itemPlano['error']);
                }

                if ($itemPlano['retValor'] && !$vendaItens[$i]['id_live_turma']) {
                    throw new Exception("(Item: " . $item . ") Turma de Live nao informada");
                }

                return ["validou"=>true, "error" => ""];
            }

        } catch(Exception $e) {
            return ["validou"=>false, "error" => $e->getMessage()];
            
        }
    }