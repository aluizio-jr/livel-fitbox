<?php
    $live_origem = $_GET['LiveOrigemID']; //1 - Youtube / 2 - Instagram / 3 - Vímeo
    $id_live_youtube = $_GET['LiveYoutubeID'];

    if ($live_origem==1) {
        echo "<div><img width='280' src='https://img.youtube.com/vi/" . $id_live_youtube . "/hqdefault.jpg'></div>";

    } else if($live_origem==2) {
        echo "<div><img width='280' src='http://fitgroup.com.br/treinoemsuacasa/app/assets/instagram_live.png'></div>";

    } else {
        echo "<div></div>";
    }


?>