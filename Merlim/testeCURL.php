<?php
    
	$ch = curl_init();
	
	// informar URL e outras fun��es ao CURL
	curl_setopt($ch, CURLOPT_URL, "http://www.google.com/");

	// Acessar a URL e envi�-la ao browser
	curl_exec($ch);
?>
