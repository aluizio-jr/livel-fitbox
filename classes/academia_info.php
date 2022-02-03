<?php
    function academia_dados($id_cliente) {

        $conn = bd_connect_fitgroup();

        if ($conn) {
           

            $str_sql = "SELECT " .
            "mobile_clientes_config.cliente_nome_cifra, " .
            "mobile_clientes_config.cliente_email_contato, " .
            "mobile_clientes_config.cliente_celular_contato " .
            "FROM " .
            "mobile_clientes_config " .
            "WHERE " .
            "mobile_clientes_config.id_cliente = '" . $id_cliente . "' " .
            "AND mobile_clientes_config.mobile_ativo = 1";

            $rs_info = mysqli_query($conn, $str_sql);	   
            $num_info = mysqli_num_rows($rs_info); 

            if ($num_info > 0) {
                $arr_result = mysqli_fetch_assoc($rs_info);
                /*
                while($r = mysql_fetch_assoc($rs_info)) {
                    $arr_result[] = $r;
                }                         
                */
            }

        }

        return  $arr_result;

    }

    function academia_aluno($id_aluno_gestor) {

        $conn = bd_connect_cv();

        if ($conn) {

            $str_sql = "SELECT c001_alunos.c000_id_cliente " .
            "FROM " .
            "c001_alunos " .
            "WHERE " .
            "c001_alunos.c001_id_aluno_gestor = '" . $id_aluno_gestor . "' " .
            "LIMIT 1;";

            $rs_acad = mysqli_query($conn, $str_sql);	   
            $num_acad = mysqli_num_rows($rs_acad);    


            if ($num_acad > 0) {

                $r = mysqli_fetch_assoc($rs_acad);
                $id_cliente = $r['c000_id_cliente'];
            } else {
                $id_cliente = $str_sql;
            }

            
        }
        
        return $id_cliente;

    }

    function academia_load_ht($id_cliente) {
        $conn = bd_connect_cv();

        if (!$conn) {
            $arr_result = array('Registros' => 0, 'AcademiaDados'=> false, 'ErroMsg' => 'Falha de conexão com o banco de dados.');
    
        } else {

            $str_sql = "SELECT 
            c000_clientes.c000_id_cliente AS AcademiaID,
            c000_clientes.c000_cliente_nome_exibicao AS AcademiaNome,
            IF(c000_clientes.c000_cliente_logo_extensao IS NOT NULL, CONCAT('" . $id_cliente . ".', c000_clientes.c000_cliente_logo_extensao),NULL) AS AcademiaLogo,
            c000_clientes.c000_cliente_slogan AS AcademiaSlogan,
           (SELECT
            SUM(m045b_sms_conta.m045b_sms_quantidade)
            FROM
            m045b_sms_conta
            WHERE
            m045b_sms_conta.c000_id_cliente = c000_clientes.c000_id_cliente 
            AND m045b_sms_conta.m045b_sms_validacao = 1  
            AND m045b_sms_conta.m045b_credito_liberado_SN = 'S') AS AcademiaSaldoSMS,
            (SELECT cv002_config.cv002_config_email 
            FROM cv002_config 
            WHERE cv002_config.c000_id_cliente = c000_clientes.c000_id_cliente 
            LIMIT 1) AS AcademiaEmail,
            (SELECT cv002_config.cv002_config_live_habilitada 
            FROM cv002_config 
            WHERE cv002_config.c000_id_cliente = c000_clientes.c000_id_cliente 
            LIMIT 1) AS AcademiaLiveHabilitada,
            (SELECT cv002_config.cv002_config_treino_habilitado 
            FROM cv002_config 
            WHERE cv002_config.c000_id_cliente = c000_clientes.c000_id_cliente 
            LIMIT 1) AS AcademiaTreinoHabilitado,
            (SELECT cv002_config.cv002_config_video_aula_habilitada 
            FROM cv002_config 
            WHERE cv002_config.c000_id_cliente = c000_clientes.c000_id_cliente 
            LIMIT 1) AS AcademiaVideoAulaHabilitada,
            (SELECT cv003_reserva_habilitada 
            FROM cv003_reserva_config 
            WHERE cv003_reserva_config.c000_id_cliente = c000_clientes.c000_id_cliente 
            LIMIT 1) AS AcademiaReservaHabilitada
            FROM c000_clientes 
            WHERE c000_clientes.c000_id_cliente = " . $id_cliente;


            $rs_cliente = mysqli_query($conn, $str_sql);	   
            $num_cliente = mysqli_num_rows($rs_cliente);    
            
            if ($num_cliente > 0){
                
                while($r = mysqli_fetch_assoc($rs_cliente)) {
                    $arr_cliente[] = $r;
                }      
                
                $arr_result = array('Registros'=> $num_cliente,'AcademiaDados'=>$arr_cliente,'ErroMsg'=>false);

            } else {
                $arr_result = array('Registros' => 0, 'AcademiaDados'=> false,'ErroMsg'=>'Cliente não encontrado','SQL'=>$str_sql);
            
            }
        }  

        return $arr_result;
    }

    function academia_load($id_cliente) {

        $conn = academia_conecta($id_cliente);
       
        if ($conn['conexao']) {

            $str_sql = "SELECT
            h066_empresa_dados.h066_nome_fantasia AS NomeAcademia,
            h066_empresa_dados.h066_endereco AS EnderecoLogradouro,
            h066_empresa_dados.h066_numero AS EnderecoNumero,
            h066_empresa_dados.h066_complemento AS EnderecoComplemento,
            h066_empresa_dados.h066_bairro AS EnderecoBairro,
            h066_empresa_dados.h066_cep AS EnderecoCEP,
            h017b_cidades.h017b_cidade_nome AS EnderecoCidade,
            h017a_estados.h017a_uf_sigla AS EnderecoUF,
            h066_empresa_dados.h066_site AS Site,
            h066_empresa_dados.h066_maps AS MapsLink
            FROM
            h066_empresa_dados
            INNER JOIN h017b_cidades ON h066_empresa_dados.h017_id_paises = h017b_cidades.h017_id_paises AND h066_empresa_dados.h017a_id_uf = h017b_cidades.h017a_id_uf AND h066_empresa_dados.h017b_id_cidade = h017b_cidades.h017b_id_cidade
            INNER JOIN h017a_estados ON h017b_cidades.h017_id_paises = h017a_estados.h017_id_paises AND h017b_cidades.h017a_id_uf = h017a_estados.h017a_id_uf
            ";

            $rs_acad = mysqli_query($conn['conexao'],$str_sql);	   
            $num_acad = mysqli_num_rows($rs_acad); 

            if ($num_acad > 0) {
                $arr_acad = mysqli_fetch_assoc($rs_acad);
            
            }

            $str_sql = "SELECT 
            h029_habilitada AS VendaOnlineHabilitada,
            h029_texto_header AS TextoCabecalho,
            h029_cad_endereco_obrig AS CadEnderecoObrigatorio,
            h029_cad_nascimento_obrig AS CadNascimentoObrigatorio,
            h029_cad_rg_obrig AS CadRGObrigatorio
            FROM 
            h029_venda_online_config";

            $rs_vo = mysqli_query($conn['conexao'], $str_sql);	   
            $num_vo = mysqli_num_rows($rs_vo); 

            if ($num_vo > 0) {
                $arr_vo = mysqli_fetch_assoc($rs_vo);

                $str_sql = "SELECT 
                h066b_empresa_comunicacao.cs017_id_comunicacao AS ID_TipoContato,
                cs017_comunicacoes.cs017_comunicacao_descricao AS TipoDescricao,
                h066b_empresa_comunicacao.h066b_comunicacao_descricao  AS Contato
                FROM h066b_empresa_comunicacao
                INNER JOIN cs017_comunicacoes ON cs017_comunicacoes.cs017_id_comunicacao = h066b_empresa_comunicacao.cs017_id_comunicacao 
                WHERE h066b_empresa_comunicacao.h066_show_venda_online = 1";

                $rs_com_vo = mysqli_query($conn['conexao'], $str_sql);	   
                $num_com_vo = mysqli_num_rows($rs_com_vo); 

                if ($num_com_vo > 0) {
                    //$arr_com = mysql_fetch_assoc($rs_com);
                    
                    while($r_vo = mysqli_fetch_assoc($rs_com_vo)) {
                        $arr_com_vo[] = $r_vo;
                    } 
                }

            }

            $arr_mb=array();
            $arr_com_mb=array();

            $str_sql = "SELECT 
            h028_habilitado AS MobileHabilitado,
            h028_exibe_fichas AS ExibeFichas,
            h028_exibe_quadro_horario AS ExibeQuadroHorarios,
            h028_exibe_checkin AS ExibeCheckin,
            h028_exibe_avaliacao AS ExibeAvaliacao,
            h028_exibe_milhagem AS ExibeFitcoins,
            h028_exibe_percursos AS ExibePercursos,
            h028_exibe_financeiro AS ExibeFinaceiro,
            h028_permite_renovacao AS PermiteRenovacao
            FROM 
            h028_mobile_config";

            $rs_mb = mysqli_query($conn['conexao'], $str_sql);	   
            $num_mb = mysqli_num_rows($rs_mb); 

            if ($num_mb > 0) {
                $arr_mb = mysqli_fetch_assoc($rs_mb);
 
                $str_sql = "SELECT 
                h066b_empresa_comunicacao.cs017_id_comunicacao AS ID_TipoContato,
                cs017_comunicacoes.cs017_comunicacao_descricao AS TipoDescricao,
                h066b_empresa_comunicacao.h066b_comunicacao_descricao  AS Contato
                FROM h066b_empresa_comunicacao
                INNER JOIN cs017_comunicacoes ON cs017_comunicacoes.cs017_id_comunicacao = h066b_empresa_comunicacao.cs017_id_comunicacao 
                WHERE h066b_empresa_comunicacao.h066_show_mobile = 1";

                $rs_com_mb = mysqli_query($conn['conexao'], $str_sql);	   
                $num_com_mb = mysqli_num_rows($rs_com_mb); 

                if ($num_com_mb > 0) {
                    //$arr_com = mysql_fetch_assoc($rs_com);
                    
                    while($r_mb = mysqli_fetch_assoc($rs_com_mb)) {
                        $arr_com_mb[] = $r_mb;
                    } 
                }                
            }            


            $str_sql = "SELECT
            h009d_cartao_credito_debito.cs019a_id_spay_cartao AS CartaoID,
            cs009e_cartao_administradora.cs009e_admnistradora_nome AS CartaoBandeira,
            CONCAT('cartao_bandeira_',cs009e_cartao_administradora.cs009e_id_admnistradora,'.png') AS BandeiraImg
            FROM
            h009d_cartao_credito_debito
            INNER JOIN cs009e_cartao_administradora ON h009d_cartao_credito_debito.cs009e_id_admnistradora = cs009e_cartao_administradora.cs009e_id_admnistradora
            WHERE
            h009d_cartao_credito_debito.h009d_cartao_ativo_SN = 'S' AND
            h009d_cartao_credito_debito.h009d_data_exclusao IS NULL AND
            h009d_cartao_credito_debito.cs019a_id_spay_cartao IS NOT NULL
            ORDER BY
            h009d_cartao_credito_debito.cs009e_id_admnistradora ASC";

            $rs_cc = mysqli_query($conn['conexao'], $str_sql);	   
            $num_cc = mysqli_num_rows($rs_cc); 

            if ($num_cc > 0) {
                //$arr_cc = mysqli_fetch_assoc($rs_cc);
                while($r_cc = mysqli_fetch_assoc($rs_cc)) {
                    $arr_cc[] = $r_cc;
                }                 
            }            

            $registros = $num_vo + $num_mb;

            $arr_result = array('Registros'=>$registros, 
                                'Host'=>$conn['host'],
                                'AcademiaID'=>$id_cliente,
                                'AcademiaDados'=>$arr_acad,
                                'VendasOnline'=>array('Setup'=>$arr_vo,'ExibirContatos'=>$arr_com_vo),
                                'Mobile'=>array('Setup'=>$arr_mb,'ExibirContatos'=>$arr_com_mb),
                                'CartoesBandeiras'=>$arr_cc,
                                'ErrMsg'=>'');

        } else {
            $arr_result = array('Registros'=>0,'ErrMsg'=>'Falha conexao');

        }

        return  $arr_result;

    }    
?>