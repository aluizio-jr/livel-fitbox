<?php

/*
$msg_to = "ExponentPushToken[C5MG0UF6daaCwjPw4gXqnX]";
$msg_sound = "default";
$msg_body = "Teste body msg";
//$msg_data = "data";
$msg_title = "Teste title";


$ch = curl_init();
            
curl_setopt($ch, CURLOPT_URL, "https://exp.host/--/api/v2/push/send");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);

curl_setopt($ch, CURLOPT_POST, TRUE);

curl_setopt($ch, CURLOPT_POSTFIELDS, "{
\"message\": {
    \"to\": \"" . $msg_to . "\",
    \"sound\": \"" . $msg_sound . "\",
    \"body\": \"$msg_body\",
    \"title\": \"$msg_title\"
}
}");

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
"Content-Type: application/json",
"Accept-encoding: gzip, deflate",
"Accept: application/json"
));

$response = curl_exec($ch);
curl_close($ch);


$sms_status = json_decode($response, true);

print_r($sms_status);

*/


/*
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://exp.host/--/api/v2/push/send',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array('to' => 'ExponentPushToken[C5MG0UF6daaCwjPw4gXqnX]',
                                'sound' => 'default',
                                'title' => 'Original Title',
                                'body' => 'And here is the body'),
  CURLOPT_HTTPHEADER => array(
    'Accept: application/json',
    'Accept-encoding: gzip, deflate',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

echo "fuck off";
curl_close($curl);
print_r($response);
*/

$ch = curl_init();
			
curl_setopt($ch, CURLOPT_URL, "https://exp.host/--/api/v2/push/send");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
// curl_setopt($ch, CURLOPT_HEADER, FALSE);

curl_setopt($ch, CURLOPT_POST, TRUE);

$obj_push = json_encode(array('to' => 'ExponentPushToken[C5MG0UF6daaCwjPw4gXqnX]',
'sound' => 'default',
'title' => 'Original Title',
'body' => 'Vai tomar no cu',
'data'=>array('nome'=>'Luca')));

curl_setopt($ch,CURLOPT_POSTFIELDS, $obj_push);

curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  "Content-Type: application/json",
  "Accept-encoding: gzip, deflate",
  "Accept: application/json"
));

$response = curl_exec($ch);

curl_close($ch);

print_r($response);

?>