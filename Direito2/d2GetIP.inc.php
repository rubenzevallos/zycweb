<?php
$auxIP = $_COOKIE["ipcity"];

if ($auxIP) {
	$ipCity = unserialize(base64_decode($auxIP));

	if ($ipCity->ipAddress != $_SERVER['REMOTE_ADDR']) $auxIP = null;

}
// 201.49.5.106 <-- Fortaleza
// 189.90.191.105 <-- S�o Lu�s
// 200.253.123.178 <-- Piau�

if(!$auxIP || gIP){
	$curl = new curl;

	$curl->agent      = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:2.0) Gecko/20100101 Firefox/4.0';
	$curl->baseDir    = DocumentRoot;
	$curl->baseURL    = 'http://api.ipinfodb.com';
	$curl->cookieFile = 'api.ipinfodb.com';

	$curl->open();
//	$ipCountry = $curl->get('/v3/ip-country/?format=json&key=719e093472e68a38eaa725a5703f3126428a970cef75034b8828680510b31141&ip='.$_SERVER['REMOTE_ADDR']);

	$auxIP = $curl->get('/v3/ip-city/?format=json&key=719e093472e68a38eaa725a5703f3126428a970cef75034b8828680510b31141&ip='.$_SERVER['REMOTE_ADDR']);

	$ipCity = json2Array($auxIP, 0, 0, 0);

  if ($ipCity->statusCode == 'OK') setcookie("ipcity", base64_encode(serialize($ipCity)), time()+3600*24*7); //set cookie for 1 week

}

if ($ipCity->statusCode == 'OK') {
	define('ipAddress',   $ipCity->ipAddress, 1);
	if ($ipCity->countryName == '-') $ipCity->countryName = 'BRAZIL';
	define('countryName', $ipCity->countryName, 1);

	if ($ipCity->countryCode == '-') $ipCity->countryCode = 'BR';
	define('countryCode', $ipCity->countryCode, 1);

	if ($ipCity->latitude == '0') $ipCity->latitude = '-11';
	define('latitude',    $ipCity->latitude, 1);

	if ($ipCity->longitude == '0') $ipCity->longitude = '-68.7333';
	define('longitude',   $ipCity->longitude, 1);

	if ($ipCity->timeZone == '-') $ipCity->timeZone = '-03:00';
	define('timeZone',    $ipCity->timeZone, 1);

	if ($ipCity->regionName == '-') $ipCity->regionName = 'DISTRITO FEDERAL';
	define('regionName',  $ipCity->regionName, 1);

	if ($ipCity->cityName == '-') $ipCity->cityName = 'BRASILIA';
	define('cityName',    $ipCity->cityName, 1);
	define('zipCode',     $ipCity->zipCode, 1);

} else {
	define('countryCode', 'BR', 1);
	define('countryName', 'BRAZIL', 1);
	define('timeZone',    '-03:00', 1);
	define('regionName',  'DISTRITO FEDERAL', 1);
	define('cityName',    'BRASILIA', 1);
	define('latitude',    '-11', 1);
	define('longitude',   '-68.7333', 1);

}
