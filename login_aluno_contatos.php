<?php
    function AlunoContatos($id_aluno_gestor, $contatos_validacao) {
        
        // par $contatos_validacao - se True, busca apenas e-mail e celular

        // $conn = bd_connect_fitgroup();
        $conn = bd_connect_cv();

        if (!$conn) {
            $arr_result = array('Registros' => 0, 'AlunoContatos'=> false, 'ErroMsg' => 'Falha de conexão com o banco de dados.');
    
        } else {
            $arr_com = array();

            if($contatos_validacao) {
                $com_tipo = '(3,4)';
            } else {
                $com_tipo = '(3,4,5,7,9)';
            }

            $str_com = " SELECT c001a_alunos_comunicacao.cs017_id_comunicacao AS ID_TipoContato,
            cs017_comunicacoes.cs017_comunicacao_descricao AS TipoDescricao,
            c001a_alunos_comunicacao.c001a_comunicacao_descricao AS Contato
            FROM c001a_alunos_comunicacao
            INNER JOIN cs017_comunicacoes ON cs017_comunicacoes.cs017_id_comunicacao = c001a_alunos_comunicacao.cs017_id_comunicacao 
            WHERE c001a_alunos_comunicacao.c001_id_aluno_gestor = " .  $id_aluno_gestor . 
            " AND c001a_alunos_comunicacao.cs017_id_comunicacao IN " . $com_tipo . 
            " ORDER BY c001a_alunos_comunicacao.cs017_id_comunicacao";

            $rs_com = mysqli_query($conn, $str_com);	   
            $num_com = mysqli_num_rows($rs_com);    

            if ($num_com > 0){
                while($r = mysqli_fetch_assoc($rs_com)) {
                    $arr_com[] = $r;
                }                         
                $arr_result = array('Registros'=> $num_com,'Contatos'=>$arr_com,'ErroMsg'=>false);

            } else {
                $arr_result = array('Registros' => 0, 'Contatos'=> '', 'ErroMsg'=>'Nenhum contato cadastrado'); //, 'cnn'=>$conn,'rs'=>$rs_com,'num'=>$num_com,'Sql'=>$str_com);                
            }
            
            return $arr_result;
        }
    }
?>