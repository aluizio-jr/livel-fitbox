<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . "/livel_fitbox/classes/functions.php";
    require_once $_SERVER['DOCUMENT_ROOT'] . "/livel_fitbox/classes/db_class.php";
    require_once "asaas_info.php";

    function asaasCienteGravar($idCliente, $conn, $sandbox) {
        try {
            $filters = ["c001_id_aluno_lo" => $idCliente];
    
            $retClienteAsaas = queryBuscaValor(
                'c001_alunos', 
                'c001_id_asaas', 
                $filters
            );

            $idClienteAsaas = $retClienteAsaas['retValor'];
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
            $response = utf8_encode($response);
            $retCliente = json_decode($response, true);
            
            switch (json_last_error()) {
                case JSON_ERROR_NONE:
                    echo ' - No errors';
                break;
                case JSON_ERROR_DEPTH:
                    echo ' - Maximum stack depth exceeded';
                break;
                case JSON_ERROR_STATE_MISMATCH:
                    echo ' - Underflow or the modes mismatch';
                break;
                case JSON_ERROR_CTRL_CHAR:
                    echo ' - Unexpected control character found';
                break;
                case JSON_ERROR_SYNTAX:
                    echo ' - Syntax error, malformed JSON';
                break;
                case JSON_ERROR_UTF8:
                    echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
                default:
                    echo ' - Unknown error';
                break;
            }

            if (!$retCliente['ALUNO_CADASTRO']['id'])  throw new Exception("Nao foi possivel cadastrar o cliente: ASAAS_NO_ID"); 

            $idClienteAsaas = $retCliente['ALUNO_CADASTRO']['id'];
            
            $arrCampos = [
                "c001_id_asaas" => $idClienteAsaas
            ];
            
            $arrWhere = [
                'campo_nome' => 'c001_id_aluno_lo',
                'campo_valor' => $idCliente
            ];

            $str_sql = queryUpdate('c001_alunos', $arrCampo, $arrWhere);
            mysqli_query($conn, $str_sql);
echo $str_sql;
            return ["idClienteAsaas" => $idClienteAsaas, "error" => mysqli_error($conn)];

        } catch(Exception $e) {
            return ["idClienteAsaas" => false, "error" => $e->getMessage()];
        }
    }
