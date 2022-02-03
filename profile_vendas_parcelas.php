<?php

    function AlunoProfileVendaParcelas($id_venda) {


        $conn = bd_connect_livel();

        if (!$conn) {
            $err_msg = 'N&atilde;o foi poss&iacute;vel estabelecer conex&atilde;o.';

        } else {
            $str_sql = "SELECT
                lo_aluno_cc.lo_id_aluno_cc,
                lo_aluno_cc.lo_cc_numero,
                lo_aluno_cc.lo_cc_validade,                
                cs009e_cartao_administradora.cs009e_admnistradora_nome,
                cs019a_spay_cartoes.cs009e_id_admnistradora
                FROM
                lo_aluno_cc 
                INNER JOIN lo_vendas ON lo_vendas.lo_id_aluno_cc = lo_aluno_cc.lo_id_aluno_cc
                INNER JOIN cs019a_spay_cartoes ON lo_aluno_cc.cs019a_id_spay_cartao = cs019a_spay_cartoes.cs019a_id_spay_cartao
                INNER JOIN cs009e_cartao_administradora ON cs019a_spay_cartoes.cs009e_id_admnistradora = cs009e_cartao_administradora.cs009e_id_admnistradora
                WHERE
                lo_vendas.lo_id_venda = " .  $id_venda;

            $rs_cartao = mysqli_query($conn, $str_sql);	   
            $num_cartao = mysqli_num_rows($rscartao);  
            $arr_cartao = array();

            while($rc = mysqli_fetch_assoc($rs_cartao)) {
                $cartao_numero = substr(CryptStr($rc['lo_cc_numero'],'undo'), -4);
                $cartao_bandeira = 'http://fitgroup.com.br/livel_fitbox/assets/cc_bandeiras/' . $rc['cs009e_id_admnistradora'] . '_32.png';
                $cartao_validade = date_create($rc['lo_cc_validade']);
                $cartao_validade  = date_format($cartao_validade, 'm/Y');
                $cartao_vencido = (DateDifDays($rc['lo_cc_validade']) < 0 ? true : false);

                $arr_cartao = array(
                    'BandeiraImg' => $cartao_bandeira,
                    'CartaoNumero' => $cartao_numero,
                    'CartaoValidade' => $cartao_validade,
                    'CartaoVencido' => $cartao_vencido
                );                    
            }

            $arr_transacoes = array();
            
            $str_sql = "SELECT
                lo_transacoes.lo_id_transacao,
                cs009e_cartao_administradora.cs009e_admnistradora_nome,
                cs019a_spay_cartoes.cs009e_id_admnistradora,
                lo_aluno_cc.lo_cc_numero,
                lo_aluno_cc.lo_cc_validade,
                lo_transacoes.lo_transacao_parcela,
                lo_transacoes.lo_transacao_vencimento,
                lo_transacoes.lo_transacao_periodo_ini,
                lo_transacoes.lo_transacao_periodo_fim,
                lo_transacoes.cs019b_id_spay_transacao_status,
                cs019b_spay_transacao_status.cs019b_status,
                lo_transacoes.cs019c_id_recorrencia_status,
                cs019c_recorrencia_status.cs019c_status_descricao,
                lo_transacoes.cs019e_id_retorno,
                cs019e_retorno_operadoras.cs019e_retorno_codigo,
                cs019e_retorno_operadoras.cs019e_retorno_descricao,
                cs019e_retorno_operadoras.cs019e_retorno_loja,
                cs019e_retorno_operadoras.cs019e_retorno_cliente,
                cs019e_retorno_operadoras.cs019e_retorno_status_final,
                lo_transacoes.lo_transacao_last_try,
                lo_transacoes.lo_transacao_aut_data,
                lo_transacoes.lo_transacao_status_final
                FROM
                lo_transacoes
                LEFT OUTER JOIN cs019b_spay_transacao_status ON lo_transacoes.cs019b_id_spay_transacao_status = cs019b_spay_transacao_status.cs019b_id_spay_transacao_status
                INNER JOIN cs019c_recorrencia_status ON lo_transacoes.cs019c_id_recorrencia_status = cs019c_recorrencia_status.cs019c_id_recorrencia_status
                INNER JOIN lo_aluno_cc ON lo_transacoes.lo_id_aluno_cc = lo_aluno_cc.lo_id_aluno_cc
                INNER JOIN cs019a_spay_cartoes ON lo_aluno_cc.cs019a_id_spay_cartao = cs019a_spay_cartoes.cs019a_id_spay_cartao
                LEFT OUTER JOIN cs019e_retorno_operadoras ON lo_transacoes.cs019e_id_retorno = cs019e_retorno_operadoras.cs019e_id_retorno AND cs019a_spay_cartoes.cs009g_id_operadora = cs019e_retorno_operadoras.cs009g_id_operadora
                INNER JOIN cs009e_cartao_administradora ON cs019a_spay_cartoes.cs009e_id_admnistradora = cs009e_cartao_administradora.cs009e_id_admnistradora
                WHERE
                lo_transacoes.lo_id_venda = "  .  $id_venda . "
                ORDER BY
                lo_transacoes.lo_transacao_vencimento";

            $rs_transacoes = mysqli_query($conn, $str_sql);	   
            $num_transacoes = mysqli_num_rows($rs_transacoes);  

            while($rt = mysqli_fetch_assoc($rs_transacoes)) {
                
                //Descrição - Recorrência ou Parcela
                if ($rt['lo_id_recorrencia']) {
                    $TransacaoDesc = "Período: " . $rt['lo_transacao_periodo_ini'] . " a " . $rt['lo_transacao_periodo_fim'];

                } else {
                    $TransacaoDesc = "Parcela " . $rt['lo_transacao_parcela'] . " de " . $num_transacoes;
                }

                //Vencimento e Status transação
                $dias_vencimento = DateDifDays($rt['lo_transacao_vencimento']);

                if ($dias_vencimento <= 0)  {
                    if ($rt['cs019b_id_spay_transacao_status']==1 || $rt['cs019b_id_spay_transacao_status']==31) {
                        $parcela_status = "Quitada";
                        $parcela_pendente = false;
                        $trocar_cartao = false;    
                        
                    } else {
                        $parcela_status = ($rt['cs019e_retorno_cliente'] ? $rt['cs019e_retorno_cliente'] : $rt['cs019b_status']);
                        if (!$parcela_status) $parcela_status = $rt['cs019c_status_descricao'];

                        $parcela_pendente = true;
                        $trocar_cartao = ($rt['lo_transacao_status_final'] == 1 ? true : false);                               
                    }
                } else {
                    $parcela_status = "A vencer";
                    $parcela_pendente = false;
                    $trocar_cartao = false;

                }

                $cartao_bandeira_img = 'http://fitgroup.com.br/livel_fitbox/assets/cc_bandeiras/' . $rt['cs009e_id_admnistradora'] . '_32.png';
                
                $cartao_validade = date_create($rt['lo_cc_validade']);
                $cartao_validade  = date_format($cartao_validade, 'm/Y');

                $cartao_vencido = (DateDifDays($rt['lo_cc_validade']) < 0 ? true : false);

                $arr_transacoes[] = array(
                    'TransacaoID' => $rt['lo_id_transacao'], 
                    'CartaoBandeira' => $cartao_bandeira_img,
                    'CartaoNumeroFinal' => substr(CryptStr($rt['lo_cc_numero'], 'undo'),-4),
                    'CartaoValidade' =>  $cartao_validade,
                    'CartaoVencido' => $cartao_vencido,
                    'TransacaoDescricao' => $TransacaoDesc,
                    'TransacaoVencimento' => $rt['lo_transacao_vencimento'],
                    'TransacaoStatus' => $parcela_status,
                    'TransacaoPendente' => $parcela_pendente,
                    'TrocarCartao' => $trocar_cartao
                );

                //$arr_vendas[$i]['Transacoes'][] = $rt;
            }
                                    
        }



        $arr_result = array('Registros'=>$num_transacoes,'CartaoVenda' => $arr_cartao, 'Transacoes'=>$arr_transacoes, 'Erro'=>$err_msg);

        return $arr_result;
    }
?>
