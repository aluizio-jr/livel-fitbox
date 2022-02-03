<?php 

    function ContentAtendimento($id_cliente) {
        //** $live_agenda: Past | Present | Future
        //**  $id_live: exibe uma live específica / ignora parametro $live_agenda

        $conn = bd_connect_cv();

        if (!$conn) {
            $arr_result = array('Registros' => 0, 'Atendentes'=> 0, 'ErroMsg' => 'Falha de conexão com o banco de dados.');
    
        } else {
     
            $today = date("Y-m-d");
            $dia_sewmana = date('N', strtotime($today));
            $dia_sewmana++;

            $horario = date('H:i:s');

            $str_atend = " SELECT
            cv001g_atendimentos.cv001g_atendimento_responsavel AS AtendenteNome,
            cv001g_atendimentos.cv001g_atendimento_whatsapp AS AtendenteWhatsapp
            FROM
            cv001g_atendimentos
            WHERE
            cv001g_atendimentos.c000_id_cliente = " . $id_cliente . 
            " AND cv001g_atendimentos.cv001g_atendimento_dia_semana  = " . $dia_sewmana . 
            " AND '" . $horario . "' BETWEEN cv001g_atendimentos.cv001g_atendimento_horario_ini AND cv001g_atendimentos.cv001g_atendimento_horario_fim";

            $rs_atend = mysqli_query($conn, $str_atend);	   
            $num_atend = mysqli_num_rows($rs_atend);    

            if ($num_atend > 0){
                while($r = mysqli_fetch_assoc($rs_atend)) {
                    $arr_atend[] = $r;
                }         

                $arr_result = array('Registros'=> $num_atend,'Atendentes'=>$arr_atend,'ErroMsg'=>false); //, 'Sql'=>$str_lives);

            } else {
                $arr_result = array('Registros'=> 0,'Atendentes'=>'','ErroMsg'=>'Nenhum atendimento no horário', 'Sql'=>$str_lives);

            }

            
        }

        return $arr_result;
    }
  
?>