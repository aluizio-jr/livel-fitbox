<?php

function asaasCobrancaValida($dadosCobranca, $conn) {

    try {
        $idCliente = $dadosCobranca['idCliente'] ?: null;
        $formaPagto = $dadosCobranca['formaPagto'] ?: null;
        $idTransacao = $dadosCobranca['idTransacao'] ?: null;
        $idParcelamento = $dadosCobranca['idParcelamento'] ?: null;        
        $numParcelas = $dadosCobranca['numParcelas'] ?: null;
        $valorParcela = $dadosCobranca['valorParcela'] ?: null;
        $idCartao = $dadosCobranca['idCartao'] ?: null;
        $dadosCartao = $dadosCobranca['dadosCartao'] ?: null;
        
        if (!$idCliente)  throw new Exception('Cliente nao informado (COB)');
        
        $filters = ["c001_id_aluno_lo" => $idCliente];
        
        $retClienteAsaas = queryBuscaValor(
            'c001_alunos', 
            'c001_id_asaas', 
            $filters
        );
        $idClienteAsaas = $retClienteAsaas['retValor'];

        if (!$idClienteAsaas) {
            $retClienteAsaas = asaasCienteGravar($idCliente, $conn, 1);
            if (!$retClienteAsaas['idClienteAsaas'])  throw new Exception($retClienteAsaas['error']);
            $idClienteAsaas = $retClienteAsaas['idClienteAsaas'];
        }
        
        if (!$formaPagto)  throw new Exception('Forma de pagamento nao informada (COB)');

        if ($formaPagto == 4 || $formaPagto == 20) {
            if ($idCartao) {
                $filters = ["lo_id_aluno_cc" => $idCartao];
                $retCC = queryBuscaValor(
                    'lo_aluno_cc', 
                    'lo_cc_token', 
                    $filters
                );

                if (!$retCC['retFn'] && !count($dadosCartao)) throw new Exception("Cartao nao encontrado (COB).");

            } else if (!count($dadosCartao)) {
                throw new Exception("Cartao nao informado (cob_val).");
                
            }
        }

        if ($formaPagto == 21) {
            $filters = ["c001_id_aluno_lo" => $idCliente];
            
            $retClienteCpf = queryBuscaValor(
                'c001_alunos', 
                'c001_cpf', 
                $filters
            );
            $clientCpf = $retClienteCpf['retValor'];
            
            if (!$clientCpf) throw new Exception("CPF obrigatorio para pagamento via boleto (COB).");
        }

        if (!$idTransacao && !$idParcelamento) throw new Exception("Origem da cobranca nao informada (COB).");
        if (!$numParcelas) throw new Exception("Numero de parcelas nao informado (COB).");
        if (!$valorParcela) throw new Exception("Valor da parcela nao informado (COB).");

        return ["validou" => true, "error" => false];

    } catch(Exception $e) {
        return ["validou" => false, "error" => $e->getMessage()];
        
    }
}