<?php
    require_once "asaas_clientes.php";
    
    function asaasCobrancaCartao (
        $id_cliente, 
        $id_transacao = false, 
        $id_parcelamento = false, 
        $id_cartao = false, 
        $dados_cartao = null) {
        
        try {

            $filters = ["c001_id_aluno_lo" => $id_cliente];
        
            $clienteAsaasId = queryBuscaValor(
                'c001_alunos', 
                'c001_id_asaas', 
                $filters
            );

            if (!$clienteAsaasId) {
                //chamar funcao para cadastrar aluno no Asaas
            }

            if ($id_cartao) {
                $filters = ["lo_id_aluno_cc" => $vendaParcelas[$i]['id_cc']];
        
                $tokenCC = queryBuscaValor(
                    'lo_aluno_cc', 
                    'lo_cc_token', 
                    $filters
                );

                if (!$tokenCC) throw new Exception("Token do cartao nao encontrado.");

            } else if (is_array($dados_cartao)) {
                
            } else {
                throw new Exception("Cartao nao informado.");
            }
            
        } catch(Exception $e) {
            return ["cobrancaCartao" => false, "error" => $e->getMessage()];
            
        }
    }