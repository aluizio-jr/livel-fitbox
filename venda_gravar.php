<?php
    require_once "classes/db_class.php"; 
    require_once "classes/functions.php";
    require_once "classes/post_data.php";

    $vendaPost = getPost();

//    echo 'ID Aluno: ' . $vendaPost['id_aluno'];
//    echo "\n";
//    echo 'Data Venda: ' . $vendaPost['venda_data'];

    $venda_count = count($vendaPost);
    $itens_count = count($vendaPost['itens']);
    $parcelas_count = count($vendaPost['parcelas']);

    echo "Venda: " . $venda_count;
    echo "\n";
    echo "Itens: " . $itens_count;
    echo "\n";
    echo "Parcelas: " . $parcelas_count;
