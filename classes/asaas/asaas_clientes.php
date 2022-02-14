<?php
    require_once "asaas_info.php";

    function asaasCienteGravar($idCliente, $conn, $sandbox) {
        try {
            $filters = ["c001_id_aluno_lo" => $idCliente];
    
            $idClienteAsaas = queryBuscaValor(
                'c001_alunos', 
                'c001_id_asaas', 
                $filters
            );

            if ($idClienteAsaas) return ["idClienteAsaas" => $idClienteAsaas, "error" => false];

            $str_sql = "SELECT * FROM c001_alunos WHERE c001_id_aluno_lo = " . $idCliente;

            $rs_cliente = mysqli_query($conn, $str_sql);	   
            $num_cliente = mysqli_num_rows($rs_cliente);  
            if (!$num_cliente > 0) throw new Exception("Nao foi possivel encontrar o cliente: ASAAS_NO_LOCAL"); 

            while($r = mysqli_fetch_assoc($rs_cliente)) {
                $clienteNome =  $r['c001_nome_completo'] ?: false;
                $clienteCPF =  $r['c001_cpf'] ?: false;
                $clienteEndRua =  $r['c001_endereco_rua'] ?: false;
                $clienteEndNumero =  $r['c001_endereco_numero'] ?: false;
                $clienteEndComplemento =  $r['c001_endereco_complemento'] ?: false;
                $clienteEndBairro =  $r['c001_endereco_bairro'] ?: false;
                $clienteEndCEP =  $r['c001_endereco_cep'] ?: false;
                $clienteCelular =  $r['c001_celular'] ?: false;
                $clienteEmail =  $r['c001_email'] ?: false;
            }

            $asaasInfo = getAsaasApiInfo('2', $sandbox);
            
            $end_point = $asaasInfo['UrlBase']
            . "?Metodo=AlunoCadastro"
            . "&ClienteID=1005"
            . "&AlunoID=" . $idCliente
            . "&Nome=" . urlencode($clienteNome)
            . "&Email=" . urlencode($clienteEmail)
            . "&Telefone=" . urlencode($clienteCelular)
            . "&Celular=" . urlencode($clienteCelular)
            . "&CnpjCpf=" . urlencode($clienteCPF)
            . "&Cep=" . urlencode($clienteEndCEP)
            . "&Endereco=" . urlencode($clienteEndRua)
            . "&EndNumero=" . urlencode($clienteEndNumero)
            . "&EndComplemento=" . urlencode($clienteEndComplemento)
            . "&EndBairro=" . urlencode($clienteEndBairro)
            . "&ClienteAsaasID="
            . "&Sandbox=" . $sandbox;

            $ch = curl_init();
    
            curl_setopt($ch, CURLOPT_URL, $end_point);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
    
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json"
            ));
        
            $response = curl_exec($ch);
            curl_close($ch);
            
            $retCliente = json_decode($response, true);
            
            if (!is_array($retCliente)) throw new Exception("Nao foi possivel cadastrar o cliente: ASAAS_NO_RET"); 
            if (!$retCliente['ALUNO_CADASTRO']['id'])  throw new Exception("Nao foi possivel cadastrar o cliente: ASAAS_NO_ID"); 

            $idClienteAsaas = $retCliente['ALUNO_CADASTRO']['id'];
            
            $arrCampos = [
                "c001_id_asaas" => $parcelidClienteAsaasamentoID
            ];
            
            $arrWhere = [
                'campo_nome' => 'c001_id_aluno_lo',
                'campo_valor' => $idCliente
            ];

            $str_sql = queryUpdate('c001_alunos', $arrCampo, $arrWhere);
            mysqli_query($conn, $str_sql);

            return ["idClienteAsaas" => $idClienteAsaas, "error" => false];

        } catch(Exception $e) {
            return ["idClienteAsaas" => false, "error" => $e->getMessage()];
        }
    }
