<?php
    require_once "classes/db_class.php"; 
    require_once "classes/functions.php";
    require_once "classes/post_data.php";

    $vendaPost = getPost();

//    echo 'ID Aluno: ' . $vendaPost['id_aluno'];
//    echo "\n";
//    echo 'Data Venda: ' . $vendaPost['venda_data'];
function recursive_show_array($arr)
{
    foreach($arr as $value)
    {
        if(is_array($value))
        {
            recursive_show_array($value);
        }
        else
        {
            echo $value;
        }
    }
}

recursive_show_array($vendaPost);