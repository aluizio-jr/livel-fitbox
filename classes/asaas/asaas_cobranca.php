<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/livel_fitbox/classes/functions.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/livel_fitbox/classes/db_class.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/livel_fitbox/classes/crypt.php";
    require_once "asaas_clientes.php";
    require_once "asaas_cobranca_valida.php";
    require_once "asaas_cobranca_cartao.php";
    require_once "asaas_cobranca_boleto.php";
    require_once "asaas_cobranca_link.php";
    
    function asaasCobranca ($dadosCobranca, $conn = false) {

        try {
            if (!$conn) {
                $conn = bd_connect_livel();
                if (!$conn) throw new Exception("Nao foi possivel conectar ao banco de dados.");
            }

            // $retValidaCobranca = asaasCobrancaVaida($dadosCobranca, $conn);
            // if (!$retValidaCobranca['validou']) throw new Exception($retValidaCobranca['error']);

            $formaPagto = $dadosCobranca['formaPagto'];
            $retornoAsaas = '';

            if ($formaPagto == 4 || $formaPagto == 20) {
                $retCartao = asaasCobrancaCartao($dadosCobranca, $conn);
                $retornoAsaas = $retCartao['retornoAsaas'];

                if (!$retCartao['aprovada']) throw new Exception($retCobranca['error']);

            }

            if ($formaPagto == 21) {
                $retBoleto = asaasCobrancaBoleto($dadosCobranca, $conn);
                if (!$retBoleto['processada']) throw new Exception($retCobranca['error']);

            }

            if ($formaPagto == 22) {
                $retLinkPagto = asaasCobrancaLinkPagamento($dadosCobranca, $conn);
                if (!count($retLinkPagto['linksGerados'])) throw new Exception('Nenhum link de pagamento gerado. ' . $retLinkPagto['error']);

            }

            return ['cobrancaAsaas' => true, 'retornoAsaas' => $retornoAsaas, 'error' => false];

        } catch(Exception $e) {
            return ['cobrancaAsaas' => false, 'retornoAsaas' => $retornoAsaas, 'error' => $e->getMessage()];
            
        }
    }