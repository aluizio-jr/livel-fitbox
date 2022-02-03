<?php

    function AlunoBadges($id_aluno) {


        $conn = bd_connect_livel();

        if (!$conn) {
            $err_msg = 'N&atilde;o foi poss&iacute;vel estabelecer conex&atilde;o.';

        } else {

            $arr_badges = array();

            $str_sql = "SELECT
                c001_alunos.c001_id_aluno_lo AS id_aluno,
                (SELECT COUNT(lo_mensagens_alunos.lo_id_mensagem_aluno)
                FROM
                lo_mensagens_alunos
                INNER JOIN lo_mensagens ON lo_mensagens_alunos.lo_id_mensagem = lo_mensagens.lo_id_mensagem
                WHERE
                lo_mensagens_alunos.c001_id_aluno_lo = c001_alunos.c001_id_aluno_lo
                AND lo_mensagens_alunos.lo_mensagem_data_leitura IS NULL
                ) AS BadgeMensagens,

                (SELECT COUNT(lo_atendimentos_interacoes.id_atendimento_interacao)
                FROM lo_atendimentos_interacoes
                INNER JOIN lo_atendimentos ON lo_atendimentos_interacoes.lo_id_atendimento = lo_atendimentos.lo_id_atendimento
                WHERE lo_atendimentos.c001_id_aluno_lo = c001_alunos.c001_id_aluno_lo
                AND lo_atendimentos_interacoes.lo_interacao_data_leitura IS NULL
                AND lo_atendimentos_interacoes.lo_id_usuario IS NOT NULL
                ) AS BadgeAtendimentos,

                (SELECT
                COUNT(lo_transacoes.lo_id_transacao)
                FROM lo_transacoes
                INNER JOIN lo_vendas ON lo_vendas.lo_id_venda = lo_transacoes.lo_id_venda
                WHERE lo_vendas.c001_id_aluno_lo = c001_alunos.c001_id_aluno_lo
                AND lo_transacoes.lo_transacao_vencimento < DATE(NOW()) 
                AND (lo_transacoes.cs019b_id_spay_transacao_status NOT IN (1,31,99) OR lo_transacoes.cs019b_id_spay_transacao_status IS NULL)
                ) AS BadgeVendasPendentes
                
                FROM c001_alunos
                WHERE c001_alunos.c001_id_aluno_lo = " . $id_aluno;

            $rs_badges = mysqli_query($conn, $str_sql);	   

            while($r = mysqli_fetch_assoc($rs_badges)) {
                $arr_badges[] = $r;
            }                                     
        }

        $arr_result = array('Registros'=>count($arr_badges),'Badges'=>$arr_badges, 'Erro'=>$err_msg);

        return $arr_result;
    }
?>