<?php


	//action = 'do' / 'undo'
// Store a string into the variable which
// need to be Encrypted
//$string_crypt = "Welcome to GeeksforGeeks\n";

// Store the cipher method
	$ciphering = "AES-128-CTR";
  
// Use OpenSSl Encryption method
	$iv_length = openssl_cipher_iv_length($ciphering);
	$options = 0;
  
// Non-NULL Initialization Vector for encryption
	$encryption_iv = 'BozoVaiTomarNoCu';
	$decryption_iv = 'BozoVaiTomarNoCu';
  
// Store the encryption key
	$encryption_key = "#$5Es@**(/|?!MyAss";
	$decryption_key = "#$5Es@**(/|?!MyAss";
// Use openssl_encrypt() function to encrypt the data

	function EncryptString ($string_crypt, $ciphering, $encryption_key, $options, $encryption_iv) {
		$encryption = openssl_encrypt($string_crypt, $ciphering, $encryption_key, $options, $encryption_iv);
		return $encryption;
	}

	// Use openssl_decrypt() function to decrypt the data

	function DecryptString ($string_crypt, $ciphering, $decryption_key, $options, $decryption_iv) {
		$decryption = openssl_decrypt ($string_crypt, $ciphering, $decryption_key, $options, $decryption_iv);
		return $decryption;
	}

	if ( $_GET['str'] &&  $_GET['action']) {
		$string_crypt = $_GET['str'];
		$action  = $_GET['action'];
		
		switch ($action) {
			case "do":
				echo EncryptString ($string_crypt, $ciphering, $encryption_key, $options, $encryption_iv);
				break;
			case "undo":
				echo DecryptString ($string_crypt, $ciphering, $decryption_key, $options, $decryption_iv);
				break;
		}		
	}

	function Crypt($str, $action) {
		switch ($action) {
			case "do":
				echo EncryptString ($str, $ciphering, $encryption_key, $options, $encryption_iv);
				break;
			case "undo":
				echo DecryptString ($str, $ciphering, $decryption_key, $options, $decryption_iv);
				break;
		}
	}
?>