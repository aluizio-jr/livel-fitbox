<?php 

    function ContentLivesHorarios($id_aluno, $id_turma = NULL) {
        //** $live_agenda: Past | Present | Future
        //**  $id_live: exibe uma live específica / ignora parametro $live_agenda

        $conn = bd_connect_livel();

        if (!$conn) {
            $arr_result = array('Registros' => 0, 'LiveImagem'=>'', 'LivesHorarios'=> false, 'ErroMsg' => 'Falha de conexão com o banco de dados.');
    
        } else {
            $acessos = AlunoAcessos($id_aluno, 'aula_live');

            $arr_turmas = array();

            $sql_img = " SELECT 
                        (CASE WHEN lo_lives.lo_live_imagem IS NOT NULL 
                            THEN CONCAT('http://fitgroup.com.br/livel_fitbox/assets/' ,lo_lives.lo_live_imagem) 
                            ELSE NULL 
                        END) AS LiveImagem
                        FROM lo_lives";


            $rs_img = mysqli_query($conn, $sql_img);	   
            $num_img = mysqli_num_rows($rs_img);    

            if ($num_img > 0){
                
                while($r = mysqli_fetch_assoc($rs_img)) {
                    $live_img = $r['LiveImagem'];
                } 
            
            }

//TURMAS                
            $str_turmas = " SELECT 
                lo_lives_turmas.lo_id_live_turma AS LiveTurmaID,
                c015_usuarios.lo_usuario_nome AS LiveProfessorNome,
                lo_lives_turmas.lo_live_meeting_link AS LiveMeetingLink,
                lo_lives_turmas.lo_live_meeting_id AS LiveMeetingID,
                lo_lives_turmas.lo_live_meeting_pwd AS LiveMeetingPwd,            

                (SELECT GROUP_CONCAT( CONCAT_WS(' ',
                (CASE lo_lives_horarios.lo_live_dia_semana
                    WHEN 1 THEN 'Dom'
                    WHEN 2 THEN 'Seg'
                    WHEN 3 THEN 'Ter'
                    WHEN 4 THEN 'Qua'
                    WHEN 5 THEN 'Qui'
                    WHEN 6 THEN 'Sex'
                    WHEN 7 THEN 'Sab'
                END),
                DATE_FORMAT(lo_lives_horarios.lo_live_horario,'%H:%i')) SEPARATOR ' | ') 
                FROM lo_lives_horarios 
                WHERE lo_lives_horarios.lo_id_live_turma = lo_lives_turmas.lo_id_live_turma
                ) AS LiveTurmaHorarios 
                
                FROM lo_lives_turmas 
                INNER JOIN c015_usuarios ON lo_lives_turmas.lo_id_usuario = c015_usuarios.lo_id_usuario 
                
                WHERE 
                lo_lives_turmas.lo_live_turma_ativa = 1
                AND lo_lives_turmas.lo_live_turma_data_exclusao IS NULL";
                
                if ($id_turma) {
                    $str_turmas.= " AND lo_lives_turmas.lo_id_live_turma = " . $id_turma;

            } 

            $rs_turmas = mysqli_query($conn, $str_turmas);	   
            $num_turmas = mysqli_num_rows($rs_turmas);    

            if ($num_turmas > 0){
                
                while($r = mysqli_fetch_assoc($rs_turmas)) {
                    $arr_turmas[] = $r;
                } 

//HORÁRIOS
                $str_dias = "SELECT DISTINCT
                    lo_lives_horarios.lo_live_dia_semana AS DiaID,
                    (CASE lo_lives_horarios.lo_live_dia_semana
                    WHEN 1 THEN 'Dom'
                    WHEN 2 THEN 'Seg'
                    WHEN 3 THEN 'Ter'
                    WHEN 4 THEN 'Qua'
                    WHEN 5 THEN 'Qui'
                    WHEN 6 THEN 'Sex'
                    WHEN 7 THEN 'Sab'
                    END)  AS DiaDesc               
                    FROM
                    lo_lives_horarios
                    INNER JOIN lo_lives_turmas ON lo_lives_turmas.lo_id_live_turma = lo_lives_horarios.lo_id_live_turma
                    WHERE lo_lives_turmas.lo_live_turma_ativa = 1
                    ORDER BY
                    lo_lives_horarios.lo_live_dia_semana ASC";


                $rs_dias = mysqli_query($conn, $str_dias);	   
                $num_dias = mysqli_num_rows($rs_dias);    

                if ($num_dias > 0){
                    $i = 0;

                    while($dias = mysqli_fetch_assoc($rs_dias)) {
                        $arr_dias[] = $dias;
                        $dia_semana = $dias['DiaID'];

                        $str_horarios = "SELECT
                            lo_lives_horarios.lo_id_live_turma AS TurmaID,
                            lo_lives_horarios.lo_id_live_horario AS HorarioID,
                            DATE_FORMAT(lo_lives_horarios.lo_live_horario,'%H:%i') AS Horario
                            FROM
                            lo_lives_horarios
                            INNER JOIN lo_lives_turmas ON lo_lives_turmas.lo_id_live_turma = lo_lives_horarios.lo_id_live_turma
                            WHERE
                            lo_lives_horarios.lo_live_dia_semana = $dia_semana
                            AND lo_lives_turmas.lo_live_turma_ativa = 1                            
                            ORDER BY
                            lo_lives_horarios.lo_live_horario ASC";

                            $rs_horarios = mysqli_query($conn, $str_horarios);	   
                            $num_horarios = mysqli_num_rows($rs_horarios);    

                        while($hor = mysqli_fetch_assoc($rs_horarios)) {
                            $arr_dias[$i]['Horarios'][] = $hor;
                        }
                        
                        $i++;

                    } 
                }
                $arr_result = array('Registros'=> $num_turmas, 
                                    'LiveImagem'=>$live_img, 
                                    'LivesTurmas'=>$arr_turmas, 
                                    'LivesDias'=>$arr_dias, 
                                    'Acesso' => $acessos, 
                                    'ErroMsg'=>false); //, 'Sql'=>$str_horarios);

            } else {
                $arr_result = array('Registros' => 0, 
                                    'LiveImagem' => '', 
                                    'LivesHorarios' => '', 
                                    'LivesDias' => '', 
                                    'Acesso' => $acessos, 
                                    'ErroMsg' => 'Nenhuma turma cadastrada'); //,'Sql'=>$str_horarios);                
            }
            
            return $arr_result;
        }
    }