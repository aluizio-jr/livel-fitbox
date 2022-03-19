<?php
require_once "classes/asaas/asaas_clientes.php";

function validaCliente($vendaCliente) {
    if ($vendaCliente['id_cliente']) {
        $filters = ["c001_id_aluno_lo" => $vendaCliente['id_cliente']];
    
        $retCliente = queryBuscaValor(
            'c001_alunos', 
            'c001_id_aluno_lo', 
            $filters
        );

        $idCliente = $retCliente['retValor'];
        if ($idCliente) return ["validou" => true, "error" => false];
    }

    if (!count($vendaCliente['dados_cliente']))
        return ["validou" => false, "error" => "Dados do cliente nao informados."];
    
    if (!$vendaCliente['dados_cliente']['nome_completo'])
        return ["validou" => false, "error" => "Nome do cliente nao informado."];

    if (!$vendaCliente['dados_cliente']['cpf'])
        return ["validou" => false, "error" => "CPF do cliente nao informado."];

    if (!$vendaCliente['dados_cliente']['email'])
        return ["validou" => false, "error" => "E-mail do cliente nao informado."];

    if (!$vendaCliente['dados_cliente']['celular'])
        return ["validou" => false, "error" => "Celular do cliente nao informado."];
    
    return ["validou" => true, "error" => false];
}

    function vendaGravarCliente($clienteDados, $conn) {
        try {
            $retValidaCliente = validaCliente($clienteDados);
            if (!$retValidaCliente['validou'])  
                throw new Exception($retValidaCliente['error']);

            if ($clienteDados['id_cliente']) 
                return ["idCliente" => $clienteDados['id_cliente'], "error" =>false];
            
            $clienteID = nextID('c001_alunos', 'c001_id_aluno_lo', false, $conn);
            $arrCampos = [
                "c001_id_aluno_lo" =>  $clienteID,
                "c001_nome_completo" => $clienteDados['dados_cliente']['nome_completo'],
                "c001_cpf" => $clienteDados['dados_cliente']['cpf'],
                "c001_data_nascimento" => $clienteDados['dados_cliente']['data_nascimento'] ?: false,
                "c001_email" => $clienteDados['dados_cliente']['email'],
                "c001_celular" => $clienteDados['dados_cliente']['celular'],
                "c001_endereco_cep" => $clienteDados['dados_cliente']['endereco_cep'] ?: false,
                "c001_endereco_numero" => $clienteDados['dados_cliente']['endereco_numero'] ?: false,
                "c001_endereco_complemento" => $clienteDados['dados_cliente']['endereco_complemento'] ?: false,
                "c001_aluno_ativo" => 1
            ];

            $str_sql = queryInsert("c001_alunos", $arrCampos);

            mysqli_query($conn, $str_sql);
            $result = mysqli_affected_rows($conn);

            if ($result <= 0) throw new Exception("Nao foi possivel gravar o novo cliente (DB): " . mysqli_error($conn)); 
            
            $retCliente = asaasCienteGravar($clienteID, $conn, 1);
            if (!$retCliente['idClienteAsaas']) throw new Exception("Nao foi possivel gravar o novo cliente (ASAAS): " . $retCliente['error']);

            return ["idCliente" => $clienteID, "error" => false];


        } catch(Exception $e) {
            return ["idCliente" => false, "error" => $e->getMessage()];
            
        }
    }