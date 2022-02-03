<?php

    function alunoEnviaCodigo($id_aluno_gestor, $email, $email_novo, $celular, $celular_novo) {

        $conn = bd_connect_cv();

        $codigo_validar = mt_rand(5000,9000);

        $ret_mail = false;
        $ret_sms=false;
        
        $id_cliente = academia_aluno($id_aluno_gestor);

        $acad_info = academia_load_ht($id_cliente);

        $academia_email = $acad_info['AcademiaDados'][0]['AcademiaEmail'];
        $academia_nome = $acad_info['AcademiaDados'][0]['AcademiaNome'];

        if ($email && $academia_email && $academia_nome) {

            if (validaEmail($email)) {

                $subject = $codigo_validar . ' é o seu código de acesso.';

                $msg = "<div style='text-align:center;'>";
                $msg.= "<h2>Ol&aacute;!";
                $msg.= "<h3>Segue o c&oacute;digo para validar a sua conta no aplicativo Treino em Sua Casa.</h3>";
                $msg.= "<h2><b>" . $codigo_validar . "</b></h2>";
                $msg.= "</div>";
                
                $msg = utf8_encode($msg);

                //$ret_mail = envia_email($email, $subject, $msg, $academia_nome,  $academia_email);
                $ret_mail = envia_email($email, $subject, $msg, $academia_nome,  $academia_email);
                if ($ret_mail) {
    
                    if ($conn) {
    
                        $str_sql = " UPDATE c001_alunos 
                                    SET c001_aluno_validado = 1 
                                    WHERE c001_id_aluno_gestor = " . $id_aluno_gestor;

                        $num_upd = mysqli_query($conn,$str_sql);
                        
                        if ($email_novo){
    
                            $str_sql = " DELETE FROM c001a_alunos_comunicacao 
                                        WHERE  
                                        c001_id_aluno_gestor = " . $id_aluno_gestor . 
                                        " AND cs017_id_comunicacao = 4";

                            $num_ins = mysqli_query($conn,$str_sql);

                            $str_sql = " INSERT INTO c001a_alunos_comunicacao (
                                        c001_id_aluno_gestor,
                                        cs017_id_comunicacao,
                                        c001a_comunicacao_descricao 
                                        ) VALUES (" . 
                                        $id_aluno_gestor . "," . 
                                        "4," . 
                                        "'" . $email . "')";
    
                            $num_ins = mysqli_query($conn,$str_sql);
    
                        }
    
                    }
                }
                
            }
        }


        if ($celular) {

            $sms_id = next_id_cv("m045_sms","m045_id_sms");
            $sms_dest_id = next_id_cv("m045a_sms_destinatarios","m045a_id_owner");

            $msg = $codigo_validar . " eh o seu codigo para validar o app TREINO EM SUA CASA. Bons Treinos!";

            //echo $celular . " | " . $msg . " | " . $sms_dest_id . " | " . $academia_nome;
            //exit;
            $sms = envia_sms($celular, $msg, $sms_dest_id, $academia_nome);

            $ret_sms = $sms['Enviado'];
            //$ret_sms = $sms;

            $str_sql = " INSERT INTO m045_sms (
                m045_id_sms,
                m045_sms_mensagem,
                m045_sms_data,
                m045_sms_hora 
                ) VALUES (" . 
                $sms_id . "," . 
                "'" . $msg . "', 
                '" . date('Y-m-d') . "',
                '" . date('H:i:s') . "')";

            $num_ins = mysqli_query($conn,$str_sql);

            $str_sql = " INSERT INTO m045b_sms_conta (
                c000_id_cliente,
                m045_id_sms,
                m045b_data,
                m045b_hora,
                m045b_sms_quantidade,
                m045b_sms_validacao 
                ) VALUES (" . 
                $id_cliente . "," . 
                $sms_id . "," . 
                "'" . date('Y-m-d') . "',
                '" . date('H:i:s') . "',
                -1,
                1)";

            $num_ins = mysqli_query($conn,$str_sql);

            $str_sql = " INSERT INTO m045a_sms_destinatarios (
                m045a_id_owner,
                c001_id_aluno_gestor,
                m045_id_sms,
                m045a_sms_celular
                ) VALUES (" . 
                $sms_dest_id . "," . 
                $id_aluno_gestor . "," . 
                $sms_id . "," . 
                "'" . $celular . "')";

            $num_ins = mysqli_query($conn,$str_sql);            

            if ($celular_novo){

                $str_sql = " DELETE FROM c001a_alunos_comunicacao 
                            WHERE  
                            c001_id_aluno_gestor = " . $id_aluno_gestor . 
                            " AND cs017_id_comunicacao = 3";

                $num_ins = mysqli_query($conn,$str_sql);

                $str_sql = " INSERT INTO c001a_alunos_comunicacao (
                            c001_id_aluno_gestor,
                            cs017_id_comunicacao,
                            c001a_comunicacao_descricao 
                            ) VALUES (" . 
                            $id_aluno_gestor . "," . 
                            "3," . 
                            "'" . $celular . "')";

                $num_ins = mysqli_query($conn,$str_sql);

            }

        }

        $ret_envio = array('codigo_validacao' => $codigo_validar,'mail_sent'=>$ret_mail, 'sms_sent' => $ret_sms);

        return $ret_envio;

    }

?>