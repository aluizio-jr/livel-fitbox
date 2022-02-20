<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/livel_fitbox/classes/functions.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/livel_fitbox/classes/db_class.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/livel_fitbox/classes/crypt.php";
    require_once "asaas_clientes.php";
    require_once "asaas_cobranca_valida.php";
    
    function asaasCobranca ($dadosCobranca, $conn = false) {

        try {
            if (!$conn) {
                $conn = bd_connect_livel();
                if (!$conn) throw new Exception("Nao foi possivel conectar ao banco de dados.");
            }

            $retValidaCobranca = asaasCobrancaVaida($dadosCobranca, $conn);

            if (!$retValidaCobranca['validou']) throw new Exception($retValidaCobranca['error']);

            $formaPagto = $dadosCobranca['formaPagto'];

            if ($formaPagto == 4 || $formaPagto == 20) {
                $retCobranca = asaasCobrancaCartao($dadosCobranca, $conn);
            }

        } catch(Exception $e) {
            return ["cobrancaCartao" => false, "error" => $e->getMessage()];
            
        }
    }