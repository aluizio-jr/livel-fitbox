<?php
    require_once "classes/db_class.php"; 
    require_once "classes/functions.php";
    require_once "classes/post_data.php";

    $venda_data = getPost();

    print_r($venda_data);
