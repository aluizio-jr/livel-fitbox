<?php 

    function ContentVideos($id_cliente) {
        //** $live_agenda: Past | Present | Future
        //**  $id_live: exibe uma live específica / ignora parametro $live_agenda

        $conn = bd_connect_cv();

        if (!$conn) {
            $arr_result = array('Registros' => 0, 'VideoCategorias'=> false, 'ErroMsg' => 'Falha de conexão com o banco de dados.');
    
        } else {
            $arr_categ = array();
            $tt_categ=0;

            $str_categ = " SELECT
            cv001k_video_aulas_categias.cv001k_id_video_aula_categia,
            cv001k_video_aulas_categias.cv001k_video_aula_categia_descricao,
            cv001k_video_aulas_categias.cv001k_video_aula_categia_order
            FROM
            cv001k_video_aulas_categias
            WHERE
            cv001k_video_aulas_categias.c000_id_cliente = " . $id_cliente . 
            " ORDER BY
            cv001k_video_aulas_categias.cv001k_video_aula_categia_order ASC ";
              

            $rs_categ = mysqli_query($conn, $str_categ);	   
            $num_categ = mysqli_num_rows($rs_categ);    

            if ($num_categ > 0){
                while($r = mysqli_fetch_assoc($rs_categ)) {
                    //$arr_categ[] = $r;
                    $id_categ = $r['cv001k_id_video_aula_categia'];

                    $str_videos = " SELECT
                    cv001f_video_aulas.cv001f_id_video_aula AS VideoID,
                    cv001f_video_aulas.cv001f_video_aula_descricao AS VideoDescricao,
                    cv001f_video_aulas.cv001f_id_video_aula_link AS VideoLink,
                    cv001f_video_aulas.cv001f_id_video_aula_order AS VideoOrder
                    FROM
                    cv001f_video_aulas
                    WHERE
                    cv001f_video_aulas.c000_id_cliente = " . $id_cliente .  
                    " AND cv001f_video_aulas.cv001k_id_video_aula_categia = " .  $id_categ . 
                    " ORDER BY cv001f_video_aulas.cv001f_id_video_aula_order ASC";
        
                    $rs_videos = mysqli_query($conn, $str_videos);	   
                    $num_videos = mysqli_num_rows($rs_videos);    
        
                    if ($num_videos > 0){
                        $arr_videos = array();
                        
                        $tt_categ++;

                        while($rv = mysqli_fetch_assoc($rs_videos)) {
                            $arr_videos[] = $rv;
                        }

                        $arr_video_aulas[] = array('CategoriaID'=>$r['cv001k_id_video_aula_categia'], 'CategoriaDescricao'=>$r['cv001k_video_aula_categia_descricao'] , 'CategoriaOrdem'=>$r['cv001k_video_aula_categia_order'], 'CategoriaVideos'=>$arr_videos);
                    }

                    
                }                         
                $arr_result = array('Registros'=> $tt_categ,'VideoCategorias'=>$arr_video_aulas,'ErroMsg'=>false); //, 'Sql'=>$str_categ);

            } else {               
                $arr_result = array('Registros' => 0, 'VideoCategorias'=> false, 'ErroMsg' => 'Nenhum vídeo encontrado.');
            }
            
            return $arr_result;
        }
    }


  
?>