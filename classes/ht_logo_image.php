<?php
    $id_academia = $_GET['id_academia'];
    $img_extensao = $_GET['img_extensao'];

    $img_logo = $id_academia . "." . $img_extensao;

    if (file_exists("../../app/assets/logos/" . $img_logo)) {
        $arr_img = getimagesize('http://fitgroup.com.br/treinoemsuacasa/app/assets/logos/'.$img_logo);
        $resize = ($arr_img[0] > $arr_img[1]) ? 'width':'height';
                    
        echo "<div><img style='" . $resize . ": 100%; object-fit: contain;' src='http://fitgroup.com.br/treinoemsuacasa/app/assets/logos/" . $img_logo ."'></div>";

    } else {
        echo "no_image: " . $img_logo;
    }

    

?>