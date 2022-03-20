<?php
    require_once "classes/asaas/asaas_cobranca_cartao.php";
    require_once "classes/asaas/asaas_cobranca_valida.php";
    require_once "classes/asaas/asaas_cobranca.php";

    function vendaGravarParcelas($clienetId, $vendaId, $vendaParcelas, $conn, $dataVenda = false) {
        try {
            if (!$dataVenda) $dataVenda = date('Y-m-d');
            $totalParcelas = 0;

            $parcelasCount = count($vendaParcelas);
            $processarAsaas = array();
            $parcelasGravou = 0;

            for ($i = 0; $i < $parcelasCount; $i++) {

                $formaPagto = $vendaParcelas[$i]['forma_pagamento'];
                $vencimento = $vendaParcelas[$i]['vencimento'];
                $valorTotalParcelas = $vendaParcelas[$i]['valor'];
                $numParcelas = $vendaParcelas[$i]['parcelas'];
                $valorParcela = $valorTotalParcelas > 0 ? ($valorTotalParcelas / $numParcelas) : 0;

                $dataPagamento = null;
                if ($formaPagto == 1 || $formaPagto == 14 || $formaPagto == 18) 
                    $dataPagamento = $vendaParcelas[$i]['data_pagamento'];
                
                $isParcelamento = ($numParcelas > 1 && ($formaPagto == 4 || $formaPagto == 21));
                //$processarCartao = (($formaPagto == 4 || $formaPagto == 20) && !$isParcelamento && DateDifDays($vencimento) <=0);
                $processarCartao = (($formaPagto == 4 || $formaPagto == 20) && !$isParcelamento);
                $processarBoleto = ($formaPagto == 21 && !$isParcelamento );
                $processarLink = ($formaPagto == 22);

                $parcelamentoID = null;
                $linkParcelas = null;
                // $boletoTransacaoId = mull;
                // $linkTransacaoId = null;
                
                if ($processarLink) {
                    $linkParcelas = $numParcelas;
                    $numParcelas = 1;
                    $valorParcela = $valorTotalParcelas;
                }

                if ($isParcelamento) {
                    $parcelamentoID = nextID('h009y_parcelamentos', 'h009y_id_parcelamento', false, $conn);
                    $arrCampos = [
                        "h009y_id_parcelamento" => $parcelamentoID,
                        "h009y_data_transacao" => date('Y-m-d'),
                        "h009y_valor" => $valorTotalParcelas,
                        "h009y_parcelas" => $numParcelas,
                        "cs009f_id_forma_pagamento" => $formaPagto
                    ];

                    $str_sql = queryInsert("h009y_parcelamentos", $arrCampos);

                    mysqli_query($conn, $str_sql);
                    $result = mysqli_affected_rows($conn);
    
                    if($result <= 0) {                
                        throw new Exception("Nao foi possivel gravar a venda (parcelamento): " . mysqli_error($conn)); 
                    }
                }

                $arrTransacoes = array();

                for ($z = 0; $z < $numParcelas; $z++) {
                    $parcelaNum++;
                    $transacaoID = nextID('lo_transacoes', 'lo_id_transacao', false, $conn);
                    if (!$transacaoID) throw new Exception("Nao foi possivel gerar o ID da transacao (BD).");

                    $dayArg = ' + ' . ($z * 30) . ' days';
                    $transacaoVencimento = date('Y-m-d', strtotime($vencimento . $dayArg)); 

                    $arrCampos = [
                        "lo_id_transacao" =>  $transacaoID,
                        "lo_id_venda" =>  $vendaId,
                        "lo_transacao_parcela" => $parcelaNum,
                        "h009y_id_parcelamento" => $parcelamentoID ?: false,
                        "lo_transacao_vencimento" => $transacaoVencimento,
                        "lo_transacao_valor" => str_replace(',', '.', $valorParcela),
                        "cs009f_id_forma_pagamento" => $formaPagto,
                        "lo_transacao_data_conciliacao" => $dataPagamento ?: false,
                        "lo_transacao_data_pagamento" => $dataPagamento ?: false
                    ];

                    $str_sql = queryInsert("lo_transacoes", $arrCampos);

                    mysqli_query($conn, $str_sql);
                    $result = mysqli_affected_rows($conn);
    
                    if ($result <= 0) throw new Exception("Nao foi possivel gravar a venda (parcela " . $i . "): " . mysqli_error($conn)); 
                    
                    $arrTransacoes[] = $transacaoID;
                }

                if ($processarCartao) {
                    $processarAsaas[] = [
                        'formaPagto' => $formaPagto,
                        'idTransacao' => $transacaoID,
                        'idParcelamento' => null,
                        'arrTransacoes' => $arrTransacoes,
                        'numParcelas' => 1,
                        'valorParcela' => $valorParcela,
                        'vencimentoParcela' => $transacaoVencimento,
                        'idCliente' => $clienetId,
                        'idVenda' => $vendaId,
                        'idCartao'=> $vendaParcelas[$i]['id_cartao'], 
                        'dadosCartao' => $vendaParcelas[$i]['dados_cc']
                    ];
                    
                } else if ($isParcelamento && $parcelamentoID) {
                    $processarAsaas[] = [
                        'formaPagto' => $formaPagto,
                        'idTransacao' => null,
                        'idParcelamento' => $parcelamentoID,
                        'arrTransacoes' => $arrTransacoes,
                        'numParcelas' => $numParcelas,
                        'valorParcela' => $valorTotalParcelas,
                        'vencimentoParcela' => $vencimento,
                        'idCliente' => $clienetId,
                        'idVenda' => $vendaId,
                        'idCartao'=> $vendaParcelas[$i]['id_cartao'] ?: null, 
                        'dadosCartao' => $vendaParcelas[$i]['dados_cc'] ?: null
                    ];          

                } else if ($processarBoleto) {
                    $processarAsaas[] = [
                        'formaPagto' => $formaPagto,
                        'idTransacao' => $transacaoID,
                        'idParcelamento' => null,
                        'arrTransacoes' => $arrTransacoes,
                        'numParcelas' => 1,
                        'valorParcela' => $valorParcela,
                        'vencimentoParcela' => $transacaoVencimento,
                        'idCliente' => $clienetId,
                        'idVenda' => $vendaId,
                        'idCartao'=> null, 
                        'dadosCartao' => null
                    ];  
                
                } else if ($processarLink && $linkParcelas) {
                    $processarAsaas[] = [
                        'formaPagto' => $formaPagto,
                        'idTransacao' => $transacaoID,
                        'idParcelamento' => null,
                        'arrTransacoes' => $arrTransacoes,
                        'numParcelas' => $linkParcelas,
                        'valorParcela' => $valorParcela,
                        'vencimentoParcela' => $transacaoVencimento,
                        'idCliente' => $clienetId,
                        'idVenda' => $vendaId,
                        'idCartao'=> null, 
                        'dadosCartao' => null
                    ]; 
                }
                
                $parcelasGravou++;
                
            }
            
            foreach ($processarAsaas as $dadosCobranca) {
                $retValidaCobranca = asaasCobrancaValida($dadosCobranca, $conn);
                if (!$retValidaCobranca['validou']) throw new Exception($retValidaCobranca['error']);
            }
  
            foreach ($processarAsaas as $dadosCobranca) {
                $retAsaas = asaasCobranca($dadosCobranca, $conn);
                
                foreach ($arrTransacoes as $transacaoID) {
                    $transacaoLogID = nextID('lo_transacoes_log', 'lo_id_log_transacao', false, $conn);

                    $arrCampos = [
                        "lo_id_log_transacao" =>  $transacaoLogID,
                        "lo_id_transacao" =>  $transacaoID,
                        "cs009f_id_forma_pagamento" =>  $dadosCobranca['formaPagto'],
                        "lo_log_data_horario" => date('Y-m-d') . ' ' . date('H:i:s'),
                        "lo_log_descricao" => $retAsaas['error'],
                        "lo_log_retorno" =>  $retAsaas['retornoAsaas']
                    ];
                    
                    $str_sql = queryInsert("lo_transacoes_log", $arrCampos);
                    
                    mysqli_query($conn, $str_sql);
                    $result = mysqli_affected_rows($conn);
                }

                if (!$retAsaas['cobrancaAsaas']) {
                    foreach ($arrTransacoes as $transacaoID) {

                        //GRAVA LOG DE FALHA
                        $transacaoLogID = nextID('lo_transacoes_log', 'lo_id_log_transacao', false, $conn);
                        $arrCampos = [
                            "lo_id_log_transacao" =>  $transacaoLogID,
                            "lo_id_transacao" =>  $transacaoID,
                            "cs009f_id_forma_pagamento" =>  $dadosCobranca['formaPagto'],
                            "lo_log_data_horario" => date('Y-m-d') . ' ' . date('H:i:s'),
                            "lo_log_descricao" => $retAsaas['error'],
                            "lo_log_retorno" =>  $retAsaas['retornoAsaas']
                        ];
                        
                        $str_sql = queryInsert("lo_transacoes_log", $arrCampos);
                        
                        mysqli_query($conn, $str_sql);
                        $result = mysqli_affected_rows($conn);
                        
                        //GRAVA PARCELA COMO PENDENTE
                        $arrCampos = [
                            'cs009f_id_forma_pagamento' => '8',
                            'lo_transacao_data_pagamento' => false,
                            'lo_transacao_data_conciliacao' => false
                        ];
                        
                        $arrWhere = [
                            'campo_nome' => 'lo_id_transacao',
                            'campo_valor' => $transacaoID
                        ];
            
                        $str_sql = queryUpdate('lo_transacoes', $arrCampos, $arrWhere);
                        mysqli_query($conn, $str_sql);
                        
                    }
                }
            }

            return ["vendaParcelas" => true, "error" => false];

        } catch(Exception $e) {
            return ["vendaParcelas" => false, "error" => $e->getMessage()];
            
        }
    }                