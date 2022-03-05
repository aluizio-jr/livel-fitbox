<?php

    function asaasCobrancaRetorno($retornoAsaas, $dadosCobranca, $conn) {
        try {
            if (!$retornoAsaas['COBRANCA']['id']) {
                $retError = $retornoAsaas['COBRANCA']['errors']['description'];
                if (!$retError) {
                    $retError = 'Erro processamento cobranca (NO_DESC): ' . json_encode($retornoAsaas, true);
                }

                throw new Exception($retError);
            } 

            $idCliente = $dadosCobranca['idCliente'];
            $idVenda = $dadosCobranca['idVenda'];
            $formaPagto = $dadosCobranca['formaPagto'];
            $idTransacao = $dadosCobranca['idTransacao'] ?: false;
            $idParcelamento = $dadosCobranca['idParcelamento'] ?: false;
            $arrTransacoes = $dadosCobranca['arrTransacoes'];
            $numParcelas = $dadosCobranca['numParcelas'];
            $valorParcela = $dadosCobranca['valorParcela'];
            $idCartao  = $dadosCobranca['idCartao'] ?: false;

            if (($formaPagto == 4 || $formaPagto == 20) && $idCartao) {
                $filters = ["lo_id_aluno_cc" => $idCartao];
                $retCC = queryBuscaValor(
                    'lo_aluno_cc', 
                    'lo_cc_token', 
                    $filters
                );
                
                $tokenCC = $retCC['retValor'];
            }

            $cobrancaAsaasID = $retornoAsaas['COBRANCA']['id'];
            $parcelamentoAsaasID = $idParcelamento ? $retornoAsaas['COBRANCA']['installment'] : null;

            $cobrancaStatusEnum = $retornoAsaas['COBRANCA']['status'];
            $filters = ["cs009q_asaas_enum" => $cobrancaStatusEnum];
            $retStatus = queryBuscaValor(
                'cs009q_cobranca_status', 
                'cs009q_id_cobranca_status', 
                $filters
            );
            $cobrancaStatusID = $retStatus['retValor'];

            $dataConcilia = $retornoAsaas['COBRANCA']['estimatedCreditDate'];
            $dataConfirmed = $retornoAsaas['COBRANCA']['confirmedDate'];
            $reciboLink = $retornoAsaas['COBRANCA']['transactionReceiptUrl'];

//ATUALIZA TOKEN
            if (($formaPagto == 4 || $formaPagto == 20) && !$tokenCC) {
                if ($retornoAsaas['COBRANCA']['creditCard']['creditCardToken']) {
                    $arrCampos = [
                        'c001n_cartao_token' => $retornoAsaas['COBRANCA']['creditCard']['creditCardToken']
                    ];
                    
                    $arrWhere = [
                        'campo_nome' => 'c001n_id_spay_aluno_cartao',
                        'campo_valor' => $idCartao
                    ];
        
                    $str_sql = queryUpdate('c001n_spay_alunos_cartoes', $arrCampos, $arrWhere);
                    mysqli_query($conn, $str_sql);                    
                }
            }   

//ATUALIZA PARCELAMENTO            
            if ($parcelamentoAsaasID && $idParcelamento) {
                $arrCampos = [
                    'h009y_id_asaas' => $parcelamentoAsaasID,
                    'h009y_data_transacao' => $dataConfirmed ?: date('Y-m-d')
                ];
                
                $arrWhere = [
                    'campo_nome' => 'h009y_id_parcelamento',
                    'campo_valor' => $idParcelamento
                ];
    
                $str_sql = queryUpdate('h009y_parcelamentos', $arrCampos, $arrWhere);
                mysqli_query($conn, $str_sql);
                
                $arrParam = [
                    'Metodo' => 'ParcelamentoList',
                    'ClienteID' => 1005,
                    'ParcelamentoID' => $idClienteAsaas,
                    'Xml' => 0,
                    'Sandbox' => 1
                ];
                    
                $urlParams = http_build_query($arrParam);
                $url = "https://fitgroup.com.br/vysor_pay_asaas/vysorpay_asaas.php";
                $getUrl = $url."?".$urlParams;
    
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_URL, $getUrl);
                curl_setopt($ch, CURLOPT_TIMEOUT, 80);
    
                $response = curl_exec($ch);
                curl_close($ch);                
                
                $retAsaas = $response['data'];
                //if(curl_error($ch))  throw new Exception('Request Error: ' . curl_error($ch));
    
                //return ["idClienteAsaas" => $idClienteAsaas, "error" => mysqli_error($conn)];
                
            } else if($cobrancaAsaasID) {
                $retAsaas = ['data'=> $retornoAsaas];
            }

            $lastIdx = count($retAsaas['data']);
            $lastIdx--;
            for ($i = $lastIdx; $i >=0; $i--) {

//CRIA CONTA_MOV_ASAAS                
                $idMovAsaas = nextID('h009h_asaas_movimento', 'h009h_id_asaas_movimento');
                if (!$idMovAsaas) throw new Exception('Nao foi possivel gerar o ID da transacao (BD).');

                $arrCampos = [
                    'h009h_id_asaas_movimento' =>  $idMovAsaas,
                    'h009h_paymentId' => $retAsaas['data'][$i]['id'],
                    'cs009q_id_cobranca_status' => $cobrancaStatusID,
                    'h009h_recibo_link' => $retAsaas['data'][$i]['transactionReceiptUrl'],
                    'h009h_conciliado' => '0',                    
                    'cs009u_id_transacao_tipo' => '0'
                ];

                $str_sql = queryInsert('h009h_asaas_movimento', $arrCampos);

                mysqli_query($conn, $str_sql);
                $result = mysqli_affected_rows($conn);

//ATUALIZA CONTA_MOV                
                $arrCampos = [
                    'h009h_id_asaas_movimento' => $idMovAsaas,
                    'h009y_id_parcelamento' => $idParcelamento ?: false,
                    'lo_transacao_data_pagamento' => ($formaPagto == 4 || $formaPagto == 20) ? $retAsaas['data'][$i]['estimatedCreditDate'] : false,x
                    'lo_transacao_data_conciliacao' => ($formaPagto == 4 || $formaPagto == 20) ? $retAsaas['data'][$i]['estimatedCreditDate'] : false
                ];
                
                $arrWhere = [
                    'campo_nome' => 'lo_id_transacao',
                    'campo_valor' => $arrTransacoes[0]
                ];
    
                $str_sql = queryUpdate('lo_transacoes', $arrCampos, $arrWhere);
                mysqli_query($conn, $str_sql);                

            }

            return ['retCobrancaRetorno' => true, 'error' => false];

        } catch(Exception $e) {
            return ['retCobrancaRetorno' => false, 'error' => $e->getMessage()];
            
        }
    }

    function cobrancaUpdate() {

    }