<?php
    require_once "classes/db_class.php"; 
    require_once "classes/functions.php";
    require_once "classes/post_data.php";

    $vendaPost = getPost();

//    echo 'ID Aluno: ' . $vendaPost['id_aluno'];
//    echo "\n";
//    echo 'Data Venda: ' . $vendaPost['venda_data'];
foreach ($vendaPost as $row)
{
    foreach($row as $i => $a)
    {
        echo $i.": ".$a;
    }
}