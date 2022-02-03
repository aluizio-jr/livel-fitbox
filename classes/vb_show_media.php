<?php
    error_reporting(0);	

    $Action = $_GET['Action']; //check_file | show_media_file   | show_media_remote | check_url
    $MediaSource = $_GET['MediaSource'];
    $MediaTipo = $_GET['MediaTipo'];    //V: vídeo | I: imagem | VM: vímeo | Y: youtube | A: flash 
    $UrlCheck = $_GET['UrlCheck'];

    //echo $Action . "/" . $MediaSource . "/" . $MediaTipo;
    //exit;

    $MediaWith = $_GET['MediaWith'];
    $MediaHeight = $_GET['MediaHeight'];

    if ($Action=="check_file") {
        $arquivo_found = file_exists("../assets/$MediaSource");
        //echo $arquivo_found;

    } else if ($Action=="show_media_file") {
        showMediaFile($MediaSource, $MediaTipo, $MediaWith, $MediaHeight);
    
    } else if ($Action=="show_media_remote") {
        showMediaRemote($MediaSource, $MediaTipo);
    
    } else if ($Action=="check_url") {
        echo urlCheckExists($UrlCheck);
    }

    function showMediaFile($media_source, $media_tipo, $media_with, $media_height) {

        if (file_exists("../assets/$media_source")) {


            if ($media_tipo == 'V') {
                //$str_obj = "<video width='90%' height='90%' autoplay muted>
                //            <source src='http://localhost:8080/academia_arquivos/animacoes/$media_source'>
                //            </video>";
                $str_obj = "<div style='width=100px;margin-left:auto;margin-right: auto;'>
                            <video width='60%' height='60%' muted autoplay='autoplay' controls>
                            <source src='http://fitgroup.com.br/livel_fitbox/assets/$media_source' type='video/mp4'>
                            <param name='wmode' value='opaque' />
                            <embed src='http://fitgroup.com.br/livel_fitbox/assets/$media_source' wmode='opaque'>
                            <param name='wmode' value='opaque' />
                            </embed>
                            </video>
                            </div>";            

            } else if ($media_tipo == 'I') {
                
                $arr_img = array();

                if ($media_with && $media_height) {
                    $arr_img[0] = $media_with;
                    $arr_img[1] = $media_height;

                } else {
                    $arr_img = getimagesize('http://fitgroup.com.br/livel_fitbox/assets/'.$media_source);

                }

                $resize = ($arr_img[0] > $arr_img[1]) ? 'width':'height';
                $str_obj = "<div ><img style='$resize: 100%; object-fit: contain; margin-bottom:10px' src='http://fitgroup.com.br/livel_fitbox/assets/$media_source'></div>";
                //list($width, $height, $type, $attr) = getimagesize('http://fitgroup.com.br/treinoemsuacasa/app/assets/exercicios/'.$media_source);
            
            } else if ($media_tipo == 'A') {

                    $str_obj = "<object type='application/x-shockwave-flash' width='90%' height='90%' data='http://fitgroup.com.br/livel_fitbox/assets/$media_source'>
                                <param name='movie' value='http://fitgroup.com.br/livel_fitbox/assets/$media_source'></param>
                                </object>";

            } else if ($media_tipo == 'VM') {

                $str_obj = "<iframe src='https://player.vimeo.com/video/473384720' width='640' height='361' frameborder='0' allow='autoplay; fullscreen' allowfullscreen></iframe>";                                  
            
            } else if ($media_tipo == 'Y') {
                $str_obj = "<iframe width='560' height='315' src='https://www.youtube.com/embed/5ajGnQhPiQk' frameborder='0' allow='autoplay; clipboard-write; encrypted-media; picture-in-picture' allowfullscreen></iframe>";
            }

            echo $str_obj; //"<div style='padding:1; position:relative; overflow:hidden; clear:both; background-color: green'>" .  $str_obj . "</div>";
            
        } else {
            echo "<div></div>";

        }

    }


    function showMediaRemote($media_source, $media_tipo) {


        if ($media_tipo == 'VM') {

            $str_obj = "<iframe src='". $media_source . "' width='640' height='361' frameborder='0' allow='autoplay; fullscreen' allowfullscreen></iframe>";                                  
        
        } else if ($media_tipo == 'Y') {

            $video_id = getYoutubeID($media_source);
            $str_obj = "<div style='padding:0;margin:0'><img style='width=200'; src='https://img.youtube.com/vi/" . $video_id . "/hqdefault.jpg'></div>";

        }   

        echo $str_obj; //"<div style='padding:1; position:relative; overflow:hidden; clear:both; background-color: green'>" .  $str_obj . "</div>";


    }

    function getYoutubeID($url) {
        parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
        return $my_array_of_vars['v'];          
    }

    function urlCheckExists($url) {
        return filter_var($url, FILTER_VALIDATE_URL);
    }
?>