<?php
    //$id_academia = $_GET['id_academia'];
    $arquivo_img = $_GET['img_file'];

    if (file_exists("../assets/" . $arquivo_img)) {
        $arr_img = getimagesize('http://fitgroup.com.br/livel_fitbox/assets/'.$arquivo_img);
        $resize = ($arr_img[0] > $arr_img[1]) ? 'width':'height';
                    
        echo "<div><img style='" . $resize . ": 100%; object-fit: contain;' src='http://fitgroup.com.br/livel_fitbox/assets/".$arquivo_img."'></div>";

    } else {
        echo "<div align-'center' style='font-family: Arial, Helvetica, sans-serif;font-weight:plain;font-size: 12px;color:#333333;line-height:1.5em;'>Imagem não disponível</div>";
    }

    //style=""font-family: Arial, Helvetica, sans-serif;font-weight:plain;font-size: 12px;color:#333333;line-height:1.5em;

?>