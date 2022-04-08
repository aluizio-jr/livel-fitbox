<?php
    require_once "classes/db_class.php"; 
    require_once "classes/functions.php";
    
    function validaVenda($vendaData) {
        $venda_count = count($vendaData);
        if (!$venda_count) return ["validou" => false, "error" => "Dados da venda nao informados."];
        
        // return ['validou' => false, 
        //         'error' => 'idCliente: ' . $vendaData['cliente']['id_cliente'] . 
        //         ' - dadosClienteCount: ' . count($vendaData['cliente']['dados_cliente'])
        // ];

        if (!$vendaData['cliente']['id_cliente'] && count($vendaData['cliente']['dados_cliente']) == 0) {
            return ["validou" => false, "error" => "Cliente nao informado."];
        }

        $clienteCheck = validaCliente($vendaData['cliente']);
        if (!$clienteCheck['validou'])
            return ["validou"=>false, "error" => $clienteCheck['error']];

        // if (!$vendaData['id_venda_tipo']) return ["validou"=>false, "error" => "Tipo de venda nao informado."];
        
        // if ($vendaData['id_venda_tipo'] == 3 && !$vendaData['id_venda_renovacao']) 
        //     return ["validou" => false, "error" => "ID da venda renovada nao informado."];

        return ["validou" => true, "error" => ""];
    }

    function validaVendaItens($vendaItens) {
        try {
            $itens_count = count($vendaItens);
            if (!$itens_count) throw new Exception("Itens da venda nao informados."); //return ["validou"=>false, "error" => "Itens da venda nao informados: " . $itens_count];
            
            // for ($i=0; $i<$itens_count; $i++) {
            //     $item++;
            //     if (!$vendaItens[$i]['id_plano_vigencia']) {
            //         throw new Exception("(Item: " . $item . ") ID do plano nao informado.");
            //     }

            //     $filters = [
            //         "lo_id_plano_vigencia" => $vendaItens[$i]['id_plano_vigencia'],
            //         "lo_id_produto_categoria" => 1
            //     ];

            //     $itemPlano = queryBuscaValor(
            //         'lo_plano_produtos', 
            //         ' COUNT(*) ', 
            //         $filters,
            //         ' JOIN lo_plano_vigencias USING(lo_id_plano) '
            //     );

            //     if (!$itemPlano['retFn']) {
            //         throw new Exception("(Item: " . $item . ") Erro busca categoria Live:" . $itemPlano['error']);
            //     }

            //     if ($itemPlano['retValor'] && !$vendaItens[$i]['id_live_turma']) {
            //         throw new Exception("(Item: " . $item . ") Turma de Live nao informada");
            //     }

            // }
            
            return ["validou"=>true, "error" => ""];

        } catch(Exception $e) {
            return ["validou"=>false, "error" => $e->getMessage()];
            
        }
    }

    function validaVendaParcelas($vendaParcelas) {
        try {
            $itens_count = count($vendaParcelas);
            if (!$itens_count) throw new Exception("Parcelas da venda nao informadas.");
            
            for ($i=0; $i<$itens_count; $i++) {
                $item++;
                if (!$vendaParcelas[$i]['vencimento']) {
                    throw new Exception("(Parcela: " . $item . ") Vencimento nao informado.");
                }

                $fpagList = [4, 20];
                if (in_array($vendaParcelas[$i]['forma_pagamento'], $fpagList)) {
                    if (!$vendaParcelas[$i]['id_cartao'] && !count(!$vendaParcelas[$i]['dados_cc'])) {
                        throw new Exception("(Parcela: " . $item . ") Cartao de credito nao informado.");
                    }

                    
                    if ($vendaParcelas[$i]['id_cartao']) {
                        $filters = ["lo_id_aluno_cc" => $vendaParcelas[$i]['id_cartao']];
    
                        $retCartao = queryBuscaValor(
                            'lo_aluno_cc', 
                            'lo_cc_token', 
                            $filters
                        );
                        
                        if (!$retCartao['retFn']) throw new Exception("ID do cartao nao encontrado. " . $retCartao['error']);

                    }

                    if (!$vendaParcelas[$i]['id_cartao']) {
                        if (!$vendaParcelas[$i]['dados_cc'][0]['cc_numero']) throw new Exception("Numero do cartao nao informado.");
                        if (!$vendaParcelas[$i]['dados_cc'][0]['cc_validade_mes']) throw new Exception("Mes da validade do cartao nao informado.");
                        if (!$vendaParcelas[$i]['dados_cc'][0]['cc_validade_ano']) throw new Exception("Ano da validade do cartao nao informado.");
                        if (!$vendaParcelas[$i]['dados_cc'][0]['cc_cvv']) throw new Exception("CVV do cartao nao informado.");
                        if (!$vendaParcelas[$i]['dados_cc'][0]['titular_nome']) throw new Exception("Nome do titular do cartao nao informado.");            
                    }     
                }

                $fpagList = [1, 14, 18];
                if (in_array($vendaParcelas[$i]['forma_pagamento'], $fpagList)) {
                    if (!$vendaParcelas[$i]['data_pagamento']) {
                        throw new Exception("(Parcela: " . $item . ") Data de pagamento nao informada.");
                    }
                }

                return ["validou"=>true, "error" => ""];
            }

        } catch(Exception $e) {
            return ["validou"=>false, "error" => $e->getMessage()];
            
        }
    }