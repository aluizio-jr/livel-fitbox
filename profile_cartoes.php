<?php

    function AlunoProfileCartoes($id_aluno) {


        $conn = bd_connect_livel();

        if (!$conn) {
            $err_msg = 'N&atilde;o foi poss&iacute;vel estabelecer conex&atilde;o.';

        } else {

            $arr_cc = array();

            $str_sql = "SELECT
                lo_aluno_cc.lo_id_aluno_cc,
                CONCAT('http://fitgroup.com.br/livel_fitbox/assets/cc_bandeiras/', cs009e_cartao_administradora.cs009e_id_admnistradora, '_32.png') AS BandeiraImg,
                cs009e_cartao_administradora.cs009e_admnistradora_nome,         
                lo_aluno_cc.lo_cc_numero,
                lo_aluno_cc.lo_cc_titular,                
                lo_aluno_cc.lo_cc_validade,
                PERIOD_DIFF(DATE_FORMAT(lo_aluno_cc.lo_cc_validade, '%Y%m'), DATE_FORMAT(CURRENT_DATE(), '%Y%m')) AS MesesVencer
                FROM
                lo_aluno_cc
                INNER JOIN cs019a_spay_cartoes ON lo_aluno_cc.cs019a_id_spay_cartao = cs019a_spay_cartoes.cs019a_id_spay_cartao
                INNER JOIN cs009e_cartao_administradora ON cs019a_spay_cartoes.cs009e_id_admnistradora = cs009e_cartao_administradora.cs009e_id_admnistradora
                WHERE
                lo_aluno_cc.c001_id_aluno_lo = $id_aluno 
                AND lo_aluno_cc.lo_cc_data_exclusao IS NULL
                ORDER BY lo_aluno_cc.lo_cc_validade DESC";

            $rs_cc = mysqli_query($conn, $str_sql);	   
            $num_cc = mysqli_num_rows($rs_cc);  

            while($r = mysqli_fetch_assoc($rs_cc)) {
                $cartao_num =  substr(CryptStr($r['lo_cc_numero'],'undo'), -4);
                $cartao_validade = date_create($r['lo_cc_validade']);
                $cartao_validade = date_format($cartao_validade,'m/y');
                $cartao_vencido = ($r['MesesVencer'] < 0);

                $arr_cc[] = array(
                    'CartaoID' => $r['lo_id_aluno_cc'],
                    'CartaoTitular' => $r['lo_cc_titular'],
                    'CartaoNumero' => $cartao_num,
                    'CartaoValidade' => $cartao_validade,
                    'CartaoVencido' => $cartao_vencido,
                    'BandeiraNome' => $r['cs009e_admnistradora_nome'],
                    'BandeiraImg' => $r['BandeiraImg']
                );
            }                                     
        }

        $arr_result = array('Registros'=>$num_cc,'Cartoes'=>$arr_cc, 'Erro'=>$err_msg);

        return $arr_result;
    }
?>