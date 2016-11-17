<?php
error_reporting(E_ALL & ~E_NOTICE);

session_cache_limiter('d2');
$sintCacheLimiter = session_cache_limiter();

session_cache_expire(20);
$sintCacheExpire = session_cache_expire();

session_start();

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// ob_end_flush();

set_time_limit(300);

$_REQUEST = array_change_key_case($_REQUEST, CASE_LOWER);

define('parOption', (int)$_REQUEST['o'], 1);

define('Domain', strtolower($_SERVER['SERVER_NAME']), 1);
define('RemoteAddress', $_SERVER['REMOTE_ADDR'], 1);
define('RequestMethod', strtolower($_SERVER['REQUEST_METHOD']), 1);
define('HTTPHost', strtolower($_SERVER['HTTP_HOST']), 1);
define('HTTPReferer', strtolower($_SERVER['HTTP_REFERER']), 1);
define('HTTPUserAgent', strtolower($_SERVER['HTTP_USER_AGENT']), 1);
define('ServerPort', $_SERVER["SERVER_PORT"]);
define('ScriptFilename', $_SERVER['SCRIPT_FILENAME'], 1);
define('DocumentRoot', $_SERVER['DOCUMENT_ROOT'], 1);

/* 'PATH_TRANSLATED'
'SCRIPT_NAME'
'PATH_INFO'
*/
// $arrPathInfo = pathinfo(__FILE__);

define('gCache',     (int)$_REQUEST['cache'], 1);
define('gCommand',     (int)$_REQUEST['command'], 1);

define('gPath',     $_REQUEST['p'], 1);
define('gFile',     $_REQUEST['f'], 1);

define('gFilename', gPath.'/'.gFile, 1);

define('gError',    $_REQUEST['e'], 1);

define('gId',       $_REQUEST['id'], 1);
define('gYear',     $_REQUEST['y'], 1);
define('gMonth',    $_REQUEST['m'], 1);
define('gDay',      $_REQUEST['d'], 1);
define('gSource',   $_REQUEST['s'], 1);
define('gHash',     $_REQUEST['hs'], 1);
define('gJS',       $_REQUEST['js'], 1);
define('gFeed',     $_REQUEST['feed'], 1);
define('gIP',       $_REQUEST['ip'], 1);

define('gDebug',    $_REQUEST['debug'], 1);

define('fullSearch', $_REQUEST['fs'], 1);

switch (php_uname('n')) {
  case 'hm4204': // Locaweb
    define('myUser', 'zyc9');
    define('myPassword', 'D2432*');
    define('myHost', 'mysql09.zyc.com.br');
    define('myDatabase', 'zyc9');
    define('mysqlCollate', 'latin1_general_ci', 1);
    break;

  default:
    // echo php_uname('n');
    define('myUser', 'root');
    define('myPassword', 'SuperRuben99');
    define('myHost', 'localhost');
    define('myDatabase', 'direito2');
    //define('mysqlCollate', 'utf8_general_ci', 1);
    define('mysqlCollate', 'latin1_general_ci', 1);

}
