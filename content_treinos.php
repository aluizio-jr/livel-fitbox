<?php 

    function ContentTreinosCategorias($optHasTreinos = false) {
        $conn = bd_connect_livel();

        if (!$conn) {
            $arr_result = array('Registros' => 0, 'TreinosCategorias'=> false, 'ErroMsg' => 'Falha de conexão com o banco de dados.');
    
        } else {
            $arr_categorias = array();

            $str_sql = "SELECT DISTINCT
                lo_treinos_categorias.lo_id_treino_categoria AS TreinoCategoriaID, 
                lo_treinos_categorias.lo_treino_categoria_descricao AS CategoriaDescricao, 
                (CASE WHEN lo_treinos_categorias.lo_treino_categoria_imagem IS NOT NULL 
                THEN CONCAT('http://fitgroup.com.br/livel_fitbox/assets/' ,lo_treinos_categorias.lo_treino_categoria_imagem) 
                ELSE NULL 
                END) AS CategoriaImagem,                
                (SELECT COUNT(*) 
                    FROM lo_treinos
                    WHERE lo_treinos.lo_id_treino_categoria = lo_treinos_categorias.lo_id_treino_categoria 
                    AND lo_treinos.lo_treino_ativo = 1 
                    AND lo_treinos.lo_treino_data_exclusao IS NULL
                ) AS CategoriaTotalTreinos 
                FROM
                lo_treinos_categorias
                WHERE
                lo_treino_categoria_ativa = 1 " . 
                ($optHasTreinos ? " HAVING CategoriaTotalTreinos > 0" : "") . "
                ORDER BY 
                lo_treinos_categorias.lo_treino_categoria_descricao";

            $rs_categorias = mysqli_query($conn, $str_sql);	  
            $num_categorias = mysqli_num_rows($rs_categorias); 

            if ($num_categorias > 0){
                while($r = mysqli_fetch_assoc($rs_categorias)) {
                    $arr_categorias[] = $r;
                }                         
                $arr_result = array('Registros'=> $num_categorias,'TreinosCategorias'=>$arr_categorias,'ErroMsg'=>false);  
                
            } else {
                $arr_result = array('Registros' => 0, 'TreinosCategorias'=> '', 'ErroMsg'=>'Nenhum treino cadastrado','SQL'=>$str_treinos); //, 'cnn'=>$conn,'rs'=>$rs_treinos,'num'=>$num_treinos,'Sql'=>$str_treinos);                
            }
            
            return $arr_result;
        }

    }

    function ContentTreinosDestaque() {

        $arr_treinos = array();
        $total_treinos = 0;

        $conn = bd_connect_livel();
        
        if (!$conn) {
            $arr_result = array('TreinosTotal'=> $total_treinos, 'TreinosDestaque'=> $arr_treinos); 
            return $arr_result;
        }

        $str_sql = " SELECT 
            COUNT(*) AS TotalTreinos
            FROM lo_treinos 
            WHERE
            lo_treinos.lo_treino_ativo = 1 AND
            lo_treinos.lo_treino_data_exclusao IS NULL";

        $rs_treinos = mysqli_query($conn, $str_sql);	   
        $num_treinos = mysqli_num_rows($rs_treinos);  

        if ($num_treinos > 0){
            while($t = mysqli_fetch_assoc($rs_treinos)) {
                $total_treinos = $t['TotalTreinos'];
            } 
        }

        $str_sql = "SELECT
            lo_treinos.lo_id_treino AS TreinoID, 
            lo_treinos_categorias.lo_treino_categoria_descricao AS TreinoCategoriaDescricao, 
            lo_treinos.lo_treino_nome AS TreinoNome, 
            lo_treinos.lo_treino_descricao AS TreinoDescricao, 

            (CASE WHEN lo_treinos.lo_treino_imagem IS NOT NULL 
                THEN CONCAT('http://fitgroup.com.br/livel_fitbox/assets/' ,lo_treinos.lo_treino_imagem) 
                ELSE NULL 
            END) AS TreinoImagem

            FROM
            lo_treinos
            LEFT OUTER JOIN lo_treinos_categorias ON lo_treinos_categorias.lo_id_treino_categoria = lo_treinos.lo_id_treino_categoria
            WHERE
            lo_treinos.lo_treino_ativo = 1 AND
            lo_treinos.lo_treino_data_exclusao IS NULL
            AND (lo_treinos.lo_treino_semana = 0 OR lo_treinos.lo_treino_semana IS NULL)
            ORDER BY 
            lo_treinos.lo_treino_destaque DESC, 
            lo_treinos.lo_id_treino DESC 
            LIMIT 5";

        $rs_treinos = mysqli_query($conn, $str_sql);	   
        $num_treinos = mysqli_num_rows($rs_treinos);  

        if ($num_treinos > 0){
            while($r = mysqli_fetch_assoc($rs_treinos)) {
                $arr_treinos[] = $r;
            }                         
            $arr_result = array('TreinosTotal'=> $total_treinos,
                                'TreinosDestaque'=>$arr_treinos); //, 'Sql'=>$str_treinos);
            
        } else {
            $arr_result = array('TreinosTotal'=> $total_treinos, 'TreinosDestaque'=> $arr_treinos); //, 'cnn'=>$conn,'rs'=>$rs_treinos,'num'=>$num_treinos,'Sql'=>$str_treinos);                
        }

        return $arr_result;        

    }


    function ContentTreinosList($id_aluno, $optCategoriaId = NULL, $optTreinoId = NULL) {
        
        $conn = bd_connect_livel();

        if (!$conn) {
            $arr_result = array('Registros' => 0, 'TreinosList'=> false, 'Acessos'=>'', 'ErroMsg' => 'Falha de conexão com o banco de dados.');

        } else if (!$id_aluno) {
            $arr_result = array('Registros' => 0, 'TreinosList'=> false, 'Acessos'=>'', 'ErroMsg' => 'ID aluno nao informado');
 
        } else {

            $acessos = AlunoAcessos($id_aluno, 'treinos_guiados');

            $arr_treinos = array();

            $str_sql = "SELECT
            lo_treinos.lo_id_treino AS TreinoID, 
            lo_treinos.lo_treino_semana AS TreinoSemana,
            lo_treinos.lo_id_treino_categoria AS TreinoCategoriaID, 
            lo_treinos_categorias.lo_treino_categoria_descricao AS TreinoCategoriaDescricao, 
            lo_treinos.lo_treino_nome AS TreinoNome, 
            lo_treinos.lo_treino_descricao AS TreinoDescricao, 
            (CASE WHEN lo_treinos.lo_treino_imagem IS NOT NULL 
                THEN CONCAT('http://fitgroup.com.br/livel_fitbox/assets/' ,lo_treinos.lo_treino_imagem) 
                ELSE NULL 
            END) AS TreinoImagem,
            
            @temp_prep := (SELECT
            SUM(lo_treino_exercicios.lo_treino_exercicio_preparacao)
            FROM
            lo_treino_exercicios
            WHERE
            lo_treino_exercicios.lo_id_treino = lo_treinos.lo_id_treino
            ) AS TreinoTempoPrep,
            
            @temp_exec := (SELECT
            SUM(TIME_TO_SEC(lo_treino_exercicios.lo_treino_exercicio_duracao))
            FROM
            lo_treino_exercicios
            WHERE
            lo_treino_exercicios.lo_id_treino = lo_treinos.lo_id_treino
            ) AS TreinoTempoExec,

            CAST((SEC_TO_TIME(@temp_prep + @temp_exec)) AS TIME) AS TreinoTempoTotal

            FROM
            lo_treinos
            INNER JOIN lo_treinos_categorias ON lo_treinos_categorias.lo_id_treino_categoria = lo_treinos.lo_id_treino_categoria
            WHERE
            lo_treinos.lo_treino_ativo = 1 AND
            lo_treinos.lo_treino_data_exclusao IS NULL" . 
            ($optTreinoId ? " AND lo_treinos.lo_id_treino = " . $optTreinoId : "") . 
            ($optCategoriaId ? " AND lo_treinos.lo_id_treino_categoria = " . $optCategoriaId : "") . "
            ORDER BY 
            lo_treinos.lo_treino_semana DESC, 
            lo_treinos_categorias.lo_treino_categoria_descricao, 
            lo_treinos.lo_treino_nome";

            $rs_treinos = mysqli_query($conn, $str_sql);	   
            $num_treinos = mysqli_num_rows($rs_treinos);  

            if ($num_treinos > 0){
                while($r = mysqli_fetch_assoc($rs_treinos)) {
                    $arr_treinos[] = $r;
                }                         
                $arr_result = array('Registros'=> $num_treinos,
                                    'TreinosList'=>$arr_treinos,
                                    'Permissoes'=> $acessos, 
                                    'ErroMsg'=>false); //, 'Sql'=>$str_treinos);
                
            } else {

                $arr_result = array('Registros' => 0, 'TreinosList'=> '', 'Acessos'=>'','ErroMsg'=>'Nenhum treino cadastrado','SQL'=>''); //, 'cnn'=>$conn,'rs'=>$rs_treinos,'num'=>$num_treinos,'Sql'=>$str_treinos);                
            }
            
            return $arr_result;
        }
    }



    function ContentTreinoExercicios($id_treino) {

        $conn = bd_connect_livel();

        if (!$conn) {
            $arr_result = array('Registros' => 0, 'TreinoExercicios'=> false, 'ErroMsg' => 'Falha de conexão com o banco de dados.');
    
        } else {
            $arr_exercicios = array();

            $str_exercicios = " SELECT
                lo_treino_exercicios.lo_treino_exercicio_ordem AS ExercicioOrdem, 
                lo_treino_exercicios.lo_id_exercicio AS ExercicioID, 
                lo_exercicios.lo_exercicio_descricao AS ExercicioNome, 
                lo_treino_exercicios.lo_treino_exercicio_info  AS ExercicioInstrucoes,
                (CASE WHEN lo_exercicios.lo_exercicio_video IS NOT NULL 
                        THEN CONCAT('http://fitgroup.com.br/livel_fitbox/assets/' ,lo_exercicios.lo_exercicio_video) 
                        ELSE NULL 
                END) AS ExercicioVideo, 	 
                lo_treino_exercicios.lo_treino_exercicio_preparacao AS ExercicioTempoPreparacao, 
                TIME_TO_SEC(lo_treino_exercicios.lo_treino_exercicio_duracao) AS ExercicioTempoExecucao
                FROM
                lo_treino_exercicios
                INNER JOIN lo_exercicios ON lo_treino_exercicios.lo_id_exercicio = lo_exercicios.lo_id_exercicio
                WHERE
                lo_treino_exercicios.lo_id_treino = " . $id_treino . "
                ORDER BY lo_treino_exercicio_ordem";

            $rs_exercicios = mysqli_query($conn, $str_exercicios);	   
            $num_exercicios = mysqli_num_rows($rs_exercicios);    

            if ($num_exercicios > 0){
                $i=0;
                while($r = mysqli_fetch_assoc($rs_exercicios)) {
                    $arr_exercicios[] = $r;
/*
                    $TempExecMili = TempoToMilisegundos($r['ExercicioTempoExecucao']);
                    $TempPrepMili = TempoToMilisegundos($r['ExercicioTempoPreparacao']);

                    $arr_exercicios[$i]['ExercicioTempoPreparacaoMilisegundos']=$TempPrepMili;
                    $arr_exercicios[$i]['ExercicioTempoExecucaoMilisegundos']=$TempExecMili;
                    $i++;
*/                    
                }                         
                
                $arr_result = array('Registros'=> $num_exercicios,'ExerciciosList'=>$arr_exercicios,'ErroMsg'=>false);

            } else {
                $arr_result = array('Registros' => 0, 'TreinoExercicios'=> '', 'ErroMsg'=>'Nenhum exercício encontrado','Sql'=>$str_exercicios);                
            }
            
            return $arr_result;
        }
    }        

    function ContentTreinoSemana() {

        $conn = bd_connect_livel();

        if (!$conn) {
            $arr_treino_semana = array('Registros' => 0, 'TreinoExercicios'=> false, 'ErroMsg' => 'Falha de conexão com o banco de dados.');
            return $arr_treino_semana;
        }

        $str_sql = "SELECT
        lo_treinos.lo_id_treino AS TreinoID, 
        lo_treinos.lo_id_treino_categoria AS TreinoCategoriaID, 
        lo_treinos_categorias.lo_treino_categoria_descricao AS TreinoCategoriaDescricao, 
        lo_treinos.lo_treino_nome AS TreinoNome, 
        lo_treinos.lo_treino_descricao AS TreinoDescricao, 
        (CASE WHEN lo_treinos.lo_treino_imagem IS NOT NULL 
            THEN CONCAT('http://fitgroup.com.br/livel_fitbox/assets/' ,lo_treinos.lo_treino_imagem) 
            ELSE NULL 
        END) AS TreinoImagem 	
        FROM
        lo_treinos
        INNER JOIN lo_treinos_categorias ON lo_treinos_categorias.lo_id_treino_categoria = lo_treinos.lo_id_treino_categoria
        WHERE
        lo_treinos.lo_treino_semana = 1 AND
        lo_treinos.lo_treino_ativo = 1 AND
        lo_treinos.lo_treino_data_exclusao IS NULL";

        $rs_treino_semana = mysqli_query($conn, $str_sql);	   

        while($r = mysqli_fetch_assoc($rs_treino_semana)) {
            $arr_treino_semana = $r;
        }        

        return $arr_treino_semana;

    }
?>