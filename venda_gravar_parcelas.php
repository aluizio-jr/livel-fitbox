<?php
    require_once "classes/asaas/asaas_cobranca_cartao.php"

    function vendaGravarParcelas($clienetId, $vendaId, $vendaParcelas, $conn, $dataVenda = false) {
        try {
            if (!$dataVenda) $dataVenda = date('Y-m-d');
            $totalParcelas = 0;

            $parcelasCount = count($vendaParcelas);
            for ($i = 0; $i < $parcelasCount; $i++) {

                $formaPagto = $vendaParcelas[$i]['forma_pagamento'];
                $vencimento = $vendaParcelas[$i]['vencimento'];
                $valorTotalParcelas = $vendaParcelas[$i]['parcelas'];
                $numParcelas = $vendaParcelas[$i]['parcelas'];
                $valorParcela = $valorTotalParcelas > 0 ? ($valorTotalParcelas / $numParcelas) : 0;
                $dataPagamento = $vendaParcelas[$i]['data_pagamento'] ?: null;
                
                $processarCartao = ($formaPagto == 4 || ($formaPagto == 20 && DateDifDays($vencimento) <=0));
                $processarBoleto = ($formaPagto == 21);
                $processarLink = ($formaPagto == 22);

                $parcelamentoID = null;
                $isParcelamento = ($numParcelas > 1 && ($formaPagto == 4 || $formaPagto == 21));

                if ($isParcelamento) {
                    $parcelamentoID = nextID('h009y_parcelamentos', 'h009y_id_parcelamento');
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

                for ($z = 0; $z < $parcelasCount; $z++) {
                    $parcelaNum++;
                    $transacaoID = nextID('lo_transacoes', 'lo_id_transacao');
                    if (!$transacaoID) throw new Exception("Nao foi possivel gerar o ID da transacao (BD).");

                    $dayArg = ' + ' . $z . ' days';
                    $transacaoVencimento = date('Y-m-d', strtotime($vencimento . $dayArg)); 

                    $arrCampos = [
                        "lo_id_transacao" =>  $transacaoID,
                        "lo_id_venda" =>  $vendaId,
                        "lo_transacao_parcela" => $parcelaNum,
                        "h009y_id_parcelamento" => $parcelamentoID ?: false,
                        "lo_transacao_vencimento" => $transacaoVencimento,
                        "lo_transacao_valor" => str_replace(',', '.', $valorParcela),
                        "cs009f_id_forma_pagamento" => $formaPagto,
                        "lo_transacao_data_conciliacao" => $dataPagamento ?: false
                    ];

                    $str_sql = queryInsert("lo_transacoes", $arrCampos);

                    mysqli_query($conn, $str_sql);
                    $result = mysqli_affected_rows($conn);
    
                    if ($result <= 0) throw new Exception("Nao foi possivel gravar a venda (parcela " . $i . "): " . mysqli_error($conn)); 
                    
                    if ($processarCartao && !$isParcelamento && !$cartaoTransacaoId) 
                        $cartaoTransacaoId = $transacaoID;
                }
                
                if ($processarCartao) {
                    if ($isParcelamento) {
                        if (!$parcelamentoID)  throw new Exception("ID do parcelamento nao encontrado para transacao (parcela " . $i . ")."); 
                        
                        $retAsaasCobranca = asaasCobrancaCartao (
                            $clienetId, 
                            false, 
                            $parcelamentoID, 
                            $vendaParcelas[$i]['id_cc'], 
                            $vendaParcelas[$i]['dados_cc']
                        );

                    } else if ($cartaoTransacaoId) {
                        $retAsaasCobranca = asaasCobrancaCartao (
                            $clienetId, 
                            $cartaoTransacaoId, 
                            false, 
                            $vendaParcelas[$i]['id_cc'], 
                            $vendaParcelas[$i]['dados_cc']
                        );
                        
                    } else {
                        throw new Exception("ID do parcelamento ou parcela nao encontradso para transacao (parcela " . $i . ")."); 
                    }

                }
                
                
            }

            return ["vendaParcelas" => $totalParcelas, "error" => false];

        } catch(Exception $e) {
            return ["vendaParcelas" => false, "error" => $e->getMessage()];
            
        }
    }                