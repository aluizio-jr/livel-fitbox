<?php

    function getAsaasApiInfo($PlanoID = Null, $Sandbox = 0, $Versao = 3) {

        $url_base  = "https://fitgroup.com.br/vysor_pay_asaas/vysorpay_asaas.php"; //($Sandbox == 1 ? "https://fitgroup.com.br/vysor_pay_asaas_sandbox/vysorpay_asaas.php" : "https://fitgroup.com.br/vysor_pay_asaas/vysorpay_asaas.php");

        if($Sandbox == 1) {
            $api_key = "a20295631d7267b84327e12eb6f0784ae053a0bcfe6e63a1ddc804505e882442";

        } else if ($PlanoID == 2) {
            $api_key =  "f4c099e1f279cbdf6c82e4ef6b54d4317f539ec58f7c9add9645b427887f7fcf";

        } else if ($PlanoID ==1) {
            $api_key = "644165c7bd0179e78047c3b2154064cb613cb9634c74e36c965c6f0c5db113d5";
        
        } else {
            $api_key = "";

        }

        $webHookUrl = 'https://fitgroup.com.br/vysor_pay_asaas/webhook_asaas.php';

        $ret_api = array(
            'UrlBase' => $url_base, 
            'ApiKey' => $api_key,
            'ApiVersion' => $Versao,
            'WebHookUrl' => $webHookUrl
        );

        return $ret_api;
    }

    function getClienteAsaasInfo($ClienteID = '1005', $ApiKey = false) {
        $cnn = bdConnectErp();
        $arr_asaas = array();

        $filter_field = $ApiKey ? 'asaas_apiKey' : 'id_cliente';
        $filter_value = $ApiKey ?: $ClienteID;

        if ($cnn) {
            $str_sql = "SELECT 
                c_clientes.id_cliente, 
                c_empresas_tipos.enum_asaas,
                c_clientes.id_asaas_plano,
                c_clientes.asaas_walletID, 
                c_clientes.asaas_apiKey,
                c_clientes.asaas_email,
                c_asaas_planos.taxa_cartao,
                c_asaas_planos.taxa_transacao,
                c_asaas_planos.taxa_boleto,
                c_asaas_planos.taxa_serasa,
                c_asaas_planos.taxa_nf                
                FROM 
                c_clientes 
                INNER JOIN c_empresas_tipos ON c_empresas_tipos.id_empresa_tipo = c_clientes.id_empresa_tipo
                INNER JOIN c_asaas_planos ON c_clientes.id_asaas_plano = c_asaas_planos.id_asaas_plano
                WHERE 
                c_clientes." . $filter_field . " = '" . $filter_value . "'";

            $rs_asaas = mysqli_query($cnn, $str_sql);	   
            $num_asaas = mysqli_num_rows($rs_asaas);  

            if ($num_asaas > 0) {
                while($r = mysqli_fetch_assoc($rs_asaas)) {
                    $arr_asaas = $r;
                }        
            }
        }  

        return $arr_asaas; 
    }

    function getWebhookClienteToken($justKey = true) {

        foreach($_SERVER as $key => $val) {
            if ($key == 'HTTP_ASAAS_ACCESS_TOKEN') {
                $apiToken = $val;
            }
            $arrHttp .= $key . " = " . $val . "|";
        }
        //return( $arh );
        $retFn = $justKey ? $apiToken : $arrHttp;
        return $retFn;

    }
