<?php
    require_once "classes/db_class.php"; 
    require_once "classes/functions.php";
    require_once "classes/post_data.php";

    $vendaPost = getPost();

   echo $vendaPost->id_aluno;
   echo "\n";
   echo $vendaPost->venda_data;
