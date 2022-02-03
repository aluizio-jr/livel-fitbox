<?php
    $url = $_GET['url'];

    echo $url . ' - IsValid: ' . isValidUrl($url);


    function isValidUrl($url) {
        $removeBom = function($url) { return preg_replace('/\\0/', "", $url); };
        $timeout = 10;
        $ch = curl_init();
        curl_setopt ( $ch, CURLOPT_URL, $removeBom );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_TIMEOUT, $timeout );
        $http_respond = curl_exec($ch);
        $http_respond = trim( strip_tags( $http_respond ) );
        $http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
        if ( ( $http_code == "200" ) || ( $http_code == "302" ) ) {
            return "OK";
        } else {
          // return $http_code;, possible too
          return "fucked";
        }
        curl_close( $ch );

    }

?>