<?php
    require_once "classes/asaas/asaas_cobranca_retorno.php";
    
    function asaasCobrancaCartao($dadosCobranca, $conn) {
        try {
            $response = '';

            $idCliente = $dadosCobranca['idCliente'];
            $idVenda = $dadosCobranca['idVenda'];
            $formaPagto = $dadosCobranca['formaPagto'];
            $idTransacao = $dadosCobranca['idTransacao'] ?: false;
            $idParcelamento = $dadosCobranca['idParcelamento'] ?: false;    
            $arrTransacoes = $dadosCobranca['arrTransacoes'];
            $numParcelas = $dadosCobranca['numParcelas'];
            $valorParcela = $dadosCobranca['valorParcela'];
            $idCartao  = $dadosCobranca['idCartao'] ?: false;
            $dadosCartao = $dadosCobranca['dadosCartao'] ?: null;

//DADOS DEFAULT CARTAO
            $defaultEmail = $idCliente . "@vysor.com.br";
            $defaultFone = "3140015218";
            $defaultCelular = "31971696965";
            $defaultEndCep = "31720-200";
            $defaultEndNumero = "155";
            $defaultEndCompleento = "A";

            //CARTAO CADASTRADO
            if ($idCartao) {
                $filters = ["lo_id_aluno_cc" => $idCartao];
                $retCC = queryBuscaValor(
                    'lo_aluno_cc', 
                    'lo_cc_token', 
                    $filters
                );
                
                $tokenCC = $retCC['retValor'];

                if (!$tokenCC) {
                    $str_sql = "SELECT
                        lo_aluno_cc.lo_cc_titular,
                        lo_aluno_cc.lo_cc_numero,
                        DATE_FORMAT(lo_aluno_cc.lo_cc_validade, '%m') AS validade_mes,
                        DATE_FORMAT(lo_aluno_cc.lo_cc_validade, '%Y') AS validade_ano,
                        lo_aluno_cc.lo_cc_cv,
                        lo_aluno_cc.lo_cc_cpf,
                        cs009e_cartao_administradora.cs009e_admnistradora_nome,
                        c001_alunos.c001_endereco_cep,
                        c001_alunos.c001_endereco_numero,
                        c001_alunos.c001_endereco_complemento,
                        c001_alunos.c001_email,
                        c001_alunos.c001_celular
                        FROM
                        lo_aluno_cc
                        INNER JOIN c001_alunos ON lo_aluno_cc.c001_id_aluno_lo = c001_alunos.c001_id_aluno_lo
                        INNER JOIN cs009e_cartao_administradora ON lo_aluno_cc.cs009e_id_admnistradora = cs009e_cartao_administradora.cs009e_id_admnistradora
                        WHERE
                        lo_aluno_cc.lo_id_aluno_cc = " . $idCartao;

                    $rs = mysqli_query($conn, $str_sql);	   
                    if (!$rs) throw new Exception("Nao foi possivel localizar os dados do cartao.");
                    $num_rs = mysqli_num_rows($rs);
                    while($r = mysqli_fetch_assoc($rs)) {
                        $cc_titular = $r['lo_cc_titular'];
                        $cc_numero = CryptStr($r['lo_cc_numero'],'undo');
                        $cc_validade_mes = $dadosCartao['validade_mes'];
                        $cc_validade_ano = $dadosCartao['validade_ano'];
                        $cc_cv = CryptStr($r['lo_cc_cv'],'undo');
                        $cc_bandeira = $r['cs009e_admnistradora_nome'];
                        $cc_cpf = $r['lo_cc_cpf'];
                        $cc_end_cep = $r['c001_endereco_cep'];
                        $cc_end_numero = $r['c001_endereco_numero'];
                        $cc_end_complemento = $r['c001_endereco_complemento'] ?: $defaultEndCompleento;
                        $cc_email = $dadosCartao['c001_email'] ?: $defaultEmail;
                        $cc_fone = $dadosCartao['c001_celular'] ?: $defaultFone;
                        $cc_celular = $dadosCartao['c001_celular'] ?: $defaultCelular;                        
                    }  
                }
            
                
        //DADOS CARTAO ENVIADOS
            } else if ($dadosCartao) {
                $id_bandeira = $dadosCartao['cc_bandeira'];
                $filters = ["cs009e_id_admnistradora" => $id_bandeira];
        
                $retBandeira = queryBuscaValor(
                    'cs009e_cartao_administradora', 
                    'cs009e_admnistradora_nome', 
                    $filters
                );
                $cc_bandeira = $retBandeira['retValor'];                

                $cc_titular = $dadosCartao['titular_nome'];
                $cc_numero = CryptStr($dadosCartao['cc_numero'],'do');
                $cc_validade_mes = $dadosCartao['cc_validade_mes'];
                $cc_validade_ano = $dadosCartao['cc_validade_ano'];
                $cc_cv = CryptStr($dadosCartao['cc_cv'],'do');
                $cc_cpf = $dadosCartao['titular_cpf'];
                $cc_end_cep = $dadosCartao['titular_endereco_cep'] ?: $defaultEndCep;            
                $cc_end_numero = $dadosCartao['titular_endereco_numero'] ?: $defaultEndNumero;
                $cc_end_complemento = $dadosCartao['titular_endereco_complemento'] ?: $defaultEndCompleento;
                $cc_email = $dadosCartao['titular_email'] ?: $defaultEmail;
                $cc_fone = $dadosCartao['titular_fone'] ?: $defaultFone;
                $cc_celular = $dadosCartao['titular_celular'] ?: $defaultCelular;

                $idCartao = nextID('lo_aluno_cc', 'lo_id_aluno_cc', false, $conn);
                $cartaoValidade = date("Y-m-t", strtotime($cc_validade_ano . "-" . $cc_validade_mes . "-01"));

                $arrCampos = [
                    "lo_id_aluno_cc" =>  $idCartao,
                    "c001_id_aluno_lo" =>  $idCliente,
                    "cs009e_id_admnistradora" => $id_bandeira,
                    "lo_cc_numero" => $cc_numero,
                    "lo_cc_validade" => $cartaoValidade,
                    "lo_cc_titular" => $cc_titular,
                    "lo_cc_cv" => $cc_cv,
                    "lo_cc_token" => false,
                    "lo_cc_cpf" => false,
                    "lo_cc_data_exclusao" => false
                ];

                $str_sql = queryInsert("lo_aluno_cc", $arrCampos);

                mysqli_query($conn, $str_sql);
                $result = mysqli_affected_rows($conn);
                $dadosCobranca['idCartao'] = $idCartao;
            }

            $filters = ["c001_id_aluno_lo" => $idCliente];
        
            $retClienteAsaas = queryBuscaValor(
                'c001_alunos', 
                'c001_id_asaas', 
                $filters
            );
            $idClienteAsaas = $retClienteAsaas['retValor'];
            if (!$idClienteAsaas) throw new Exception("Nao foi possivel localizar o ID cliente Asaas: " . $idCliente);

            $arrParam = array (
                'Metodo' => 'CobrancaCartao',
                'ClienteID' => 1005,
                'AlunoAsaasID' => $idClienteAsaas,
                'Vencimento' => date('Y-m-d'),
                'Valor' => str_replace(',', '.', $valorParcela),
                'ParcelasCount' => $numParcelas > 1 ? $numParcelas : '',
                'ParcelasValorTotal' => $numParcelas > 1 ? str_replace(',', '.', ($valorParcela)) : '',
                'Descricao' => 'Livel Fitbox',
                'Reference' => $idVenda,
                'CartaoToken' => $tokenCC ?: '', 
                'CartaoNumero' => $tokenCC ? '' : $cc_numero,
                'CartaoCvv' => $tokenCC ? '' : $cc_cv,
                'CartaoNome' => $tokenCC ? '' : $cc_bandeira,
                'CartaoExpMes' => $tokenCC ? '' : $cc_validade_mes,
                'CartaoExpAno' => $tokenCC ? '' : $cc_validade_ano,
                'HolderName' => $tokenCC ? '' : $cc_titular,
                'HolderEmail' => $tokenCC ? '' : $cc_email,
                'HolderCpfCnpj' => $tokenCC ? '' : $cc_cpf,
                'HolderCEP' => $tokenCC ? '' : $cc_end_cep,
                'HolderEndNumero' => $tokenCC ? '' : $cc_end_numero,
                'HolderEndComplemento' => $tokenCC ? '' : $cc_end_complemento,
                'HolderPhone' => $tokenCC ? '' : $cc_fone,
                'HolderMobile' => $tokenCC ? '' : $cc_celular,
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

            if(curl_error($ch)) throw new Exception('Request Error: ' . curl_error($ch));

            curl_close($ch);

            $response = utf8_encode($response);
            $retCobrancaCartao = json_decode($response, true);

            $cartaoRetorno = asaasCobrancaRetorno($retCobrancaCartao, $dadosCobranca, $conn);
            if (!$cartaoRetorno['retCobrancaRetorno']) throw new Exception($cartaoRetorno['error']);
            
            return ['aprovada' => true, 'retornoAsaas' => $response, 'error' => false];

        } catch(Exception $e) {
            return ['aprovada' => false, 'retornoAsaas' => $response, 'error' => $e->getMessage()];
            
        }
    }