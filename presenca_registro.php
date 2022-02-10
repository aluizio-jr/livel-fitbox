<?php

function PresencaRegistro($get) {

        $conn = bd_connect_livel();
        if ($conn) {

            if (isset($get['AlunoID'])) {
                $EpisodioID = $get['EpisodioID'] > 0 ? $get['EpisodioID'] : 'NULL';
                $TurmaID = $get['TurmaID'] > 0 ? $get['TurmaID'] : 'NULL';
                $TreinoID = $get['TreinoID'] > 0 ? $get['TreinoID'] : 'NULL';

                $str_sql = " INSERT INTO lo_acessos (
                    c001_id_aluno_lo,
                    lo_acesso_data_hora,
                    lo_id_conteudo_episodio,
                    lo_id_live_turma,
                    lo_id_treino_guiado
                    ) VALUES ("
                    . $get['AlunoID'] . ","
                    . "'" . date('Y-m-d') . " " . date('H:i:s') . "',"
                    . $EpisodioID . ","
                    . $TurmaID . ","
                    . $TreinoID . ")";

                if (mysqli_query($conn, $str_sql)) {
                    $last_id = mysqli_insert_id($conn);
                    if (!$last_id) {
                        $msg_err = 'Erro ao inserir acesso (ID)';
                        //goto ERROR_HANDLER;                  
                    }
                } else {
                    $msg_err = 'Erro ao inserir acesso (QRY): ' . $str_sql;
                    //goto ERROR_HANDLER;            
                }

            } else {
                $msg_err = 'ID aluno nao informado';

            }
        } else {
            $msg_err = 'Erro conexao banco de dados';
        }
    

    $return = array(
        'status' => $msg_err ? '403' : '200',
        'message' => $msg_err ?: 'OK'
    );

    return $return;        
}

?>