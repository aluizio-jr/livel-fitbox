<?php
header('Content-Type: application/json;charset=UTF-8');
  
       $end_point = "http://fitgroup.com.br/livel_fitbox/fitbox_api.php";
       
       $ch = curl_init();
   
       curl_setopt($ch, CURLOPT_URL, $end_point);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
       curl_setopt($ch, CURLOPT_HEADER, FALSE);
   
       curl_setopt($ch, CURLOPT_POST, TRUE);
 
       $obj_push = array(
           'AuthToken' => 'FlnPoU230Xgf',
           'Metodo' => 'PresencaRegistro',
           'AlunoID' => 1,
           'EpisodioID' => '',
           'TurmaID' => 1,
           'TreinoID' => ''
       );

       //$obj_post = json_encode($obj_push);
       
       curl_setopt($ch,CURLOPT_POSTFIELDS, $obj_post);
   
       curl_setopt($ch, CURLOPT_HTTPHEADER, array(
         "Content-Type: application/json"
       ));
   
       $response = curl_exec($ch);
       curl_close($ch);
       
       echo $response;
       // $ret = array(
       //     'Acesso' => array('EndPoint' => $end_point, 'Token' => $cliente_api_key),
       //     'Dados' => $obj_push,
       //     'RetAsaas' => json_decode($response, true)
       // );

       // return $ret;




   

