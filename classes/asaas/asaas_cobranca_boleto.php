<?php
    require_once "classes/asaas/asaas_cobranca_retorno.php";

    function asaasCobrancaBoleto($dadosCobranca, $conn) {
        try {
            $idCliente = $dadosCobranca['idCliente'];
            $idVenda = $dadosCobranca['idVenda'];
            $formaPagto = $dadosCobranca['formaPagto'];
            $idTransacao = $dadosCobranca['idTransacao'] ?: false;
            $idParcelamento = $dadosCobranca['idParcelamento'] ?: false;    
            $arrTransacoes = $dadosCobranca['arrTransacoes'];
            $numParcelas = $dadosCobranca['numParcelas'];
            $valorParcela = $dadosCobranca['valorParcela'];
            $vencimentoParcela = $dadosCobranca['vencimentoParcela'];

            $filters = ["c001_id_aluno_lo" => $idCliente];
        
            $retClienteAsaas = queryBuscaValor(
                'c001_alunos', 
                'c001_id_asaas', 
                $filters
            );
            $idClienteAsaas = $retClienteAsaas['retValor'];

            $arrParam = array (
                'Metodo' => 'CobrancaBoleto',
                'ClienteID' => 1005,
                'AlunoAsaasID' => $idClienteAsaas,
                'Vencimento' => $vencimentoParcela,
                'Valor' => str_replace(',', '.', $valorParcela),
                'ParcelasCount' => $numParcelas > 1 ? $numParcelas : '',
                'ParcelasValorTotal' => $numParcelas > 1 ? str_replace(',', '.', ($numParcelas * $valorParcela)) : '',
                'Descricao' => 'Livel Fitbox',
                'Reference' => $idVenda,
                'Multa' => 0,
                'Juros' => 0,
                'IP' => '179.152.8.87',
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
            $retCobrancaBoleto = json_decode($response, true);

            $boletoRetorno = asaasCobrancaRetorno($retCobrancaBoleto, $dadosCobranca, $conn);
            if (!$boletoRetorno['retCobrancaRetorno']) throw new Exception($boletoRetorno['error']);

            return ["processada" => true, "error" => false];

        } catch(Exception $e) {
            return ["processada" => false, "error" => $e->getMessage()];
            
        }
    }