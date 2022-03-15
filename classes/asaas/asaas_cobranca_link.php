<?php
    require_once "classes/asaas/asaas_cobranca_retorno.php";
    
    function asaasCobrancaLinkPagamento($dadosCobranca, $conn) {
        try {
            $linksGerados = array();

            $idCliente = $dadosCobranca['idCliente'];
            $filters = ["c001_id_aluno_lo" => $idCliente];
        
            $retClienteAsaas = queryBuscaValor(
                'c001_alunos', 
                'c001_id_asaas', 
                $filters
            );
            $idClienteAsaas = $retClienteAsaas['retValor'];     

            $idVenda = $dadosCobranca['idVenda'];
            $idTransacao = $dadosCobranca['idTransacao'] ?: false;  
            $arrTransacoes = $dadosCobranca['arrTransacoes'];
            $numParcelas = $dadosCobranca['numParcelas'];
            $valorParcela = $dadosCobranca['valorParcela'];
            $linkValor = $valorParcela;
            $linkValorDesconto =  $linkValor - (($linkValor * 10) / 100 );
            $dueDateLimitDays = 1;
            
            $dayArg = ' + 30 days';
            $endDate = date('Y-m-d', strtotime(date('Y-m-d') . $dayArg));

            $linkTipos = $numParcelas > 1 ? 3 : 1;

            for ($i = 1; $i <= $linkTipos; $i++) {

                switch ($i) {
                    case 1: 
                        $billingType = 'CREDIT_CARD';
                        $chargeType = $numParcelas > 1 ? 'INSTALLMENT' : 'DETACHED';
                        $valor = str_replace(',', '.', $linkValor);
                        $parcelas = $numParcelas;
                        $linkDescription = 'Livel Fitbox  (PARCELAMENTO CARTAO)';
                        $dueDateLimitDays = 1;
                        break;

                    case 2:
                        $billingType = 'BOLETO';
                        $chargeType = 'DETACHED';
                        $valor = str_replace(',', '.', ($numParcelas > 1 ? $linkValorDesconto : $linkValor));
                        $parcelas = 1;
                        $linkDescription = 'Livel Fitbox (A VISTA BOLETO)';
                        $dueDateLimitDays = 1;
                        break;

                    case 3:
                        $billingType = 'PIX';
                        $chargeType = 'DETACHED';
                        $valor = str_replace(',', '.', ($numParcelas > 1 ? $linkValorDesconto : $linkValor));
                        $parcelas = 1;
                        $linkDescription = 'Livel Fitbox (A VISTA PIX)';
                        $dueDateLimitDays = 1;
                        break;
                }

                $linkName = 'C' . $idCliente . 'V' . $idVenda . 'T' . $idTransacao . '_' . $billingType;

                $arrParam = array (
                    'Metodo' => 'CobrancaLinkPagamento',
                    'ClienteID' => 1005,
                    'AlunoAsaasID' => $idClienteAsaas,
                    'LinkNome' => $linkName,
                    'LinkDescricao' => $linkDescription,
                    'Valor' => $valor,
                    'billingType' => $billingType,
                    'chargeType' => $chargeType,
                    'Parcelas' => $parcelas,
                    'endDate' => $endDate,
                    'dueDateLimitDays' => $dueDateLimitDays,
                    'Sandbox' => 1
                );
                    
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

                if(curl_error($ch))  throw new Exception('Request Error: ' . curl_error($ch));

                curl_close($ch);

                $response = utf8_encode($response);
                $retCobrancaLink = json_decode($response, true);
                
                if (!$retCobrancaLink['LINK_PAGTO']['id']) {
                    $retError = $retCobrancaLink['LINK_PAGTO']['errors']['description'];
                    if (!$retError) {
                        $retError = 'Erro processamento link pagamento (NO_DESC): ' . json_encode($retCobrancaLink, true);
                    }
    
                    //throw new Exception($retError);
                } else {
                    $arrCampos = [
                        'lo_id_link_pagamento' => $retCobrancaLink['LINK_PAGTO']['id'],
                        'lo_id_transacao' =>  $idTransacao,
                        'name' => $retCobrancaLink['LINK_PAGTO']['name'],
                        'value' => $retCobrancaLink['LINK_PAGTO']['value'],
                        'active' => 1,
                        'chargeType' => $retCobrancaLink['LINK_PAGTO']['chargeType'],
                        'url' => $retCobrancaLink['LINK_PAGTO']['url'],
                        'billingType' => $retCobrancaLink['LINK_PAGTO']['billingType'],
                        'subscriptionCycle' => null,
                        'description' => $retCobrancaLink['LINK_PAGTO']['description'],
                        'endDate' => null,
                        'deleted' => null,
                        'viewCount' => null,
                        'maxInstallmentCount' => $retCobrancaLink['LINK_PAGTO']['maxInstallmentCount'],
                        'dueDateLimitDays' => null,
                        'notificationEnabled' => 1
                    ];
                    
                    $str_sql = queryInsert('lo_links_pagamento', $arrCampos);

                    mysqli_query($conn, $str_sql);
                    $result = mysqli_affected_rows($conn);
                    
                    $linksGerados[] = array(
                        'lo_id_link_pagamento' => $retCobrancaLink['LINK_PAGTO']['id'],
                        'name' => $retCobrancaLink['LINK_PAGTO']['name'],
                        'description' => $retCobrancaLink['LINK_PAGTO']['description'],
                        'value' => $retCobrancaLink['LINK_PAGTO']['value'],
                        'maxInstallmentCount' => $retCobrancaLink['LINK_PAGTO']['maxInstallmentCount'],
                        'url' => $retCobrancaLink['LINK_PAGTO']['url']
                    );
                }                
            }

            return ["linksGerados" => $linksGerados, "error" => false];

        } catch(Exception $e) {
            return ["linksGerados" => $linksGerados, "error" => $e->getMessage()];
            
        }
    }