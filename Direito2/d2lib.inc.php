<?php
$saveWeek = array(array('domingo', 'dom', 'd', 'domingo')
								, array('segunda', 'seg', 's', 'segunda-feira')
								, array('ter�a', 'ter', 't', 'ter�a-feira')
								, array('quarta', 'qua', 'q', 'quarta-feira')
								, array('quinta', 'qui', 'q', 'quinta-feira')
								, array('sexta', 'sex', 's', 'sexta-feira')
								, array('s�bado', 's�b', 's', 's�bado'));

$saveMonth = array(array('janeiro', 'jan', 'j')
							   , array('fevereiro', 'fev', 'f')
							   , array('mar�o', 'mar', 'm')
							   , array('abril', 'abr', 'a')
							   , array('maio', 'mai', 'm')
							   , array('junho', 'jun', 'j')
							   , array('julho', 'jul', 'j')
							   , array('agosto', 'ago', 'a')
							   , array('setembro', 'set', 's')
							   , array('outubro', 'out', 'o')
							   , array('novembro', 'nov', 'n')
							   , array('dezembro', 'dez', 'd'));

$saveUF = array('AC' => array('�cre')
							, 'AM' => array('Amaz�nas')
							, 'AP' => array('Amap�')
							, 'PA' => array('Par�')
							, 'RO' => array('Rond�nia')
							, 'RR' => array('Ror�ima')

							, 'MA' => array('Maranh�o')
							, 'CE' => array('Cear�')
							, 'PI' => array('Piau�')
							, 'RN' => array('Rio Grande do Norte')
							, 'AL' => array('Alagoas')
							, 'PB' => array('Paraiba')
							, 'PE' => array('Pernambuco')
							, 'SE' => array('Sergipe')
							, 'BA' => array('Bahia')

							, 'TO' => array('Tocantins')
							, 'DF' => array('Distrito Federal')
							, 'GO' => array('Goi�s')
							, 'MS' => array('Mato Grosso do Sul')
							, 'MT' => array('Mato Grosso')

							, 'ES' => array('Esp�rito Santo')
							, 'RJ' => array('Rio de Janeiro')
							, 'MG' => array('Minas Gerais')
							, 'SP' => array('S�o Paulo')

							, 'PR' => array('Paran�')
							, 'SC' => array('Santa Catarina')
							, 'RS' => array('Rio Grande do Sul'));

// =========================================================================
// Conecta com o SQL ativo - MSSQL
// -------------------------------------------------------------------------
function myQuery($sql) {
  if (!$myQuery = @mysql_query($sql, myConn)) {
    echo "<span style=\"text-align: left;\"><pre><b>MySQL - Query</b>:"
        ."<br /><b>Host</b>: ".myHost
        ."<br /><b>Database</b>: ".myDatabase
        ."<br /><b>Message</b>: [".mysql_error(myConn)."]";
    preg_match_all("%^(?P<sql>.*?)(?P<r>\r)?(?P<n>\n)%sm", $sql."\r\n", $Matches, PREG_OFFSET_CAPTURE);

    if (preg_last_error()) debugError("<b>preg_last_error</b>:".preg_last_error());

    $i = 0;

    $errorColumn -= 8;

    foreach ($Matches[0] as $key => $value) {
      $i++;

      $sql = $Matches["sql"][$key][0];

      echo "<br />";

      if ($i != $errorLine) {
        echo htmlentities($sql);
      } else {
        echo htmlentities($sql)." &lt-- <font color=\"red\"\><b>Error</b> </font>";
      }
    }

    echo "<hr />".debugBackTrace();
    echo "</pre></span>";
    die();
  }

  return $myQuery;

}

// =========================================================================
// Conecta com o SQL ativo - MSSQL
// -------------------------------------------------------------------------
function myConnect() {
  global $myConn;

  // $myConn = @mysql_pconnect(ConnectionStringHost, ConnectionStringUser, ConnectionStringPassword) or die("O MySQL parece estar desativado.");

  // if (ConnectionStringDatabase!="" and !@mysql_select_db(ConnectionStringDatabase)) die("O MySQL est� indispon�vel.");

  // Conex�o com o MSSQL
  // $myConn = sqlsrv_connect(myHost,array("Database"=>"credito_istcartoes_com_br"));

  if (!$myConn = @mysql_connect(myHost, myUser, myPassword)) {
    echo "<span style=\"text-align: left;\"><pre><b>MySQL - Connect</b>:"
        ."<br /><b>Connection</b>: ".myHost
        ."<br /><b>Database</b>: ".myDatabase
        ."<br /><b>Message</b>: The MySQL connection failed";

    echo "<hr />".debugBackTrace();
    echo "</pre></span>";
    die();
  }

  if (!@mysql_select_db(myDatabase, $myConn)) {
    echo "<span style=\"text-align: left;\"><pre><b>MySQL - SelectDB</b>:"
        ."<br /><b>Connection</b>: ".myHost
        ."<br /><b>Database</b>: ".myDatabase
        ."<br /><b>Message</b>: [".mysql_error($myConn)."]";

    echo "<hr />".debugBackTrace();
    echo "</pre></span>";
    die();
  }

	define('myConn', $myConn);

}

// =========================================================================
// Libera os recursos usados pela conex�o
// -------------------------------------------------------------------------
function myDisconnet() {

  if (myConn) {
    mysql_close(myConn);
  }
}

/*
SHOW VARIABLES LIKE 'have_innodb'
SHOW VARIABLES LIKE 'have_bdb'

BEGIN WORK
START TRANSACTION;
ROLLBACK
COMMIT

PASSWORD('i283kh')

SET AUTOCOMMIT=0;
EXPLAIN [EXTENDED]

*/

function debugError($varContent, $blnBackTrace=0) {

  if ($varContent) {
    $debugBackTrace = debug_backtrace();

    $strFile     = basename($debugBackTrace[0]["file"]);
    $strFunction = $debugBackTrace[1]["function"];
    $intLine     = $debugBackTrace[0]["line"];

    echo "<pre style=\"text-align: left;\"><b>$strFile($intLine)-&gt;$strFunction</b><br />";

    if (is_array($varContent)) {
      print_r($varContent);
    } else {
      echo $varContent;
    }

    if ($blnBackTrace) echo "<hr />".debugBackTrace();

    echo "</pre>";

    ob_flush(); flush();

  }
}

function debugBackTrace() {
  $debugBackTrace = debug_backtrace();

  // print_r($debugBackTrace);

  $intDebugArray = count($debugBackTrace);

  for ($i = 1; $i < $intDebugArray; $i++) {
    if ($i > 1) $return .= "-&gt;".$debugBackTrace[$i]["function"].", ";

    $return .= basename($debugBackTrace[$i]["file"])."(".$debugBackTrace[$i]["line"].")";

    $strFunction = $debugBackTrace[$i]["function"];
  }

  return $return;
}

// =========================================================================
// Conecta com o SQL ativo - MSSQL
// -------------------------------------------------------------------------
function msConnect() {
  global $msConn;

  // $msConn = @mysql_pconnect(ConnectionStringHost, ConnectionStringUser, ConnectionStringPassword) or die("O MySQL parece estar desativado.");

  // if (ConnectionStringDatabase!="" and !@mysql_select_db(ConnectionStringDatabase)) die("O MySQL est� indispon�vel.");

  // Conex�o com o MSSQL
  // $msConn = sqlsrv_connect(msHost,array("Database"=>"credito_istcartoes_com_br"));

  $msConn = @mssql_connect(msHost, msUser, msPassword) or die("<br />CONNECT: O MSSQL ".msHost." parece estar desativado.<br />[".mssql_get_last_message()."]");

  @mssql_select_db(msDatabase, $msConn) or die("<br />SELECT: O database ".msDatabase." do MSSQL ".msHost." est� indispon�vel.<br />[".mssql_get_last_message()."]");

	define('msConn', $msConn);

}

// =========================================================================
// Libera os recursos usados pela conex�o
// -------------------------------------------------------------------------
function msDisconnet() {

  if (msConn) {
    mssql_close(msConn);
  }
}

class curl {
  var $cURL;
  var $info;
  var $agent;
  var $baseURL;
  var $baseDir;
  var $referer;
  var $step;

  var $result;

  var $pattern;
  var $matches;

  function close() {
    curl_close($this->cURL);
  }

  function open() {
    $this->cURL = curl_init();

    curl_setopt($this->cURL, CURLOPT_AUTOREFERER, true);

    // you might want the headers for http codes
    // curl_setopt($this->cURL, CURLOPT_HEADER, true);

    // you may need to set the http useragent for curl to operate as
    curl_setopt($this->cURL, CURLOPT_USERAGENT, $this->agent);
    //curl_setopt($this->cURL, CURLOPT_HEADER, 1);

    // you wanna follow stuff like meta and location headers
    curl_setopt($this->cURL, CURLOPT_FOLLOWLOCATION, true);

    curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($this->cURL, CURLOPT_MAXREDIRS, 1);

    curl_setopt($this->cURL, CURLOPT_CRLF, true);
    // curl_setopt($this->cURL, CURLOPT_FRESH_CONNECT, true);


    curl_setopt($this->cURL, CURLOPT_COOKIEJAR, $this->baseDir.$this->cookieFile);
    curl_setopt($this->cURL, CURLOPT_COOKIEFILE, $this->baseDir.$this->cookieFile);

    // curl_setopt($this->cURL, CURLOPT_VERBOSE, 2);
    // curl_setopt($this->cURL, CURLOPT_STDERR, $this->baseDir.'verbose.txt');

  }

  function post($page, $vars = '') {
    if ($this->step) echo "$this->step".$this->step = '';

    if ($this->referer) curl_setopt($this->cURL, CURLOPT_REFERER, $this->referer);

    $this->referer = $this->baseURL.$page;

    // if(is_array($vars)) $vars = implode('&', $vars);

    curl_setopt($this->cURL, CURLOPT_URL, $this->baseURL.$page);

    if ($vars) {
      curl_setopt($this->cURL, CURLOPT_POST, true);
      curl_setopt($this->cURL, CURLOPT_POSTFIELDS, $vars);
    }

    $this->result = curl_exec($this->cURL);

    if (curl_errno($this->cURL)) {
      print "Error: " . curl_error($this->cURL);

    } else {
      $this->info = curl_getinfo($this->cURL);

      if ($this->step) echo ' - '.$this->info['http_code'].' ['.$this->info['size_upload'].'('.$this->info['speed_upload'].'), '.$this->info['size_download'].'('.$this->info['speed_download'].')] '.$this->info['total_time'].'s';

      $this->matches = array();

      if ($this->pattern) {
				preg_match_all($this->pattern, $this->result, $this->matches, PREG_OFFSET_CAPTURE);

				$this->pattern = null;

				if (preg_last_error()) echo "<br /><b>preg_last_error</b>:".preg_last_error();
      }
    }

    return $this->result;
  }

  function get($page) {
    if ($this->step) echo "$this->step [<a href=\"$this->baseURL$page\" target=\"_blank\">URL</a>]".$this->step = '';

    if ($this->referer) curl_setopt($this->cURL, CURLOPT_REFERER, $this->referer);

    $this->referer = $this->baseURL.$page;

    curl_setopt($this->cURL, CURLOPT_URL, $this->baseURL.$page);

    $this->result = curl_exec($this->cURL);

    if (curl_errno($this->cURL)) {
      print "Error: " . curl_error($this->cURL);

    } else {
      $this->info = curl_getinfo($this->cURL);
      if ($this->step) echo ' - '.$this->info['http_code'].' ['.$this->info['size_upload'].'('.$this->info['speed_upload'].'), '.$this->info['size_download'].'('.$this->info['speed_download'].')] '.$this->info['total_time'].'s';

      $this->matches = array();

      if ($this->pattern) {
				preg_match_all($this->pattern, $this->result, $this->matches, PREG_OFFSET_CAPTURE);

				$this->pattern = null;

				if (preg_last_error()) echo "<br /><b>preg_last_error</b>:".preg_last_error();
      }
    }

    return $this->result;
  }
}

function accentString2Latin1($strString, $blnHTML = 0, $blnQuot = 0) {
  if ($blnHTML) {
  	$latin1 = array('&#39;', '&#40;', '&#41;', '&iexcl;', '&cent;', '&pound;', '&yen;', '&brvbar;', '&slash;', '&sect;', '&copy;', '&ordf;', '&laquo;', '&not;', '&reg;', '&macr;', '&deg;', '&plusmn;', '&sup2;', '&sup3;', '&acute;', '&micro;', '&para;', '&middot;', '&cedil;', '&sup1;', '&ordm;', '&raquo;', '&frac14;', '&frac12;', '&frac34;', '&iquest;', '&Agrave;', '&Aacute;', '&Acirc;', '&Atilde;', '&Auml;', '&Aring;', '&Ccedil;', '&Egrave;', '&Eacute;', '&Ecirc;', '&Euml;', '&Igrave;', '&Iacute;', '&Icirc;', '&Iuml;', '&ETH;', '&Ntilde;', '&Ograve;', '&Oacute;', '&Ocirc;', '&Otilde;', '&Ouml;', '&times;', '&Oslash;', '&Ugrave;', '&Uacute;', '&Ucirc;', '&Uuml;', '&THORN;', '&szlig;', '&agrave;', '&aacute;', '&acirc;', '&atilde;', '&auml;', '&aelig;', '&ccedil;', '&egrave;', '&eacute;', '&ecirc;', '&euml;', '&igrave;', '&iacute;', '&icirc;', '&iuml;', '&ograve;', '&oacute;', '&ocirc;', '&otilde;', '&ouml;', '&divide;', '&ugrave;', '&uacute;', '&ucirc;', '&uuml;', '&yacute;', '&yuml;');
		$accent = array('\'', '(', ')', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�');

  } else {
  	$latin1 = array('&amp;', '&lt;', '&gt;', '&#39;', '&#40;', '&#41;', '&iexcl;', '&cent;', '&pound;', '&yen;', '&brvbar;', '&slash;', '&sect;', '&copy;', '&ordf;', '&laquo;', '&not;', '&reg;', '&macr;', '&deg;', '&plusmn;', '&sup2;', '&sup3;', '&acute;', '&micro;', '&para;', '&middot;', '&cedil;', '&sup1;', '&ordm;', '&raquo;', '&frac14;', '&frac12;', '&frac34;', '&iquest;', '&Agrave;', '&Aacute;', '&Acirc;', '&Atilde;', '&Auml;', '&Aring;', '&Ccedil;', '&Egrave;', '&Eacute;', '&Ecirc;', '&Euml;', '&Igrave;', '&Iacute;', '&Icirc;', '&Iuml;', '&ETH;', '&Ntilde;', '&Ograve;', '&Oacute;', '&Ocirc;', '&Otilde;', '&Ouml;', '&times;', '&Oslash;', '&Ugrave;', '&Uacute;', '&Ucirc;', '&Uuml;', '&THORN;', '&szlig;', '&agrave;', '&aacute;', '&acirc;', '&atilde;', '&auml;', '&aelig;', '&ccedil;', '&egrave;', '&eacute;', '&ecirc;', '&euml;', '&igrave;', '&iacute;', '&icirc;', '&iuml;', '&ograve;', '&oacute;', '&ocirc;', '&otilde;', '&ouml;', '&divide;', '&ugrave;', '&uacute;', '&ucirc;', '&uuml;', '&yacute;', '&yuml;');
		$accent = array('&', '<', '>', '\'', '(', ')', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�');

  	// $latin1 = array('&brvbar;', '&pound;', '&laquo;', '&raquo;', '&acute;', '&times;', '&divide;', '&cent;', '&ordf;', '&ordm;', '&deg;', '&amp;', '&#41;', '&#40;', '&lt;', '&gt;', '&aacute;', '&eacute;', '&iacute;', '&oacute;', '&uacute;', '&acirc;', '&ecirc;', '&icirc;', '&ocirc;', '&ucirc;', '&agrave;', '&egrave;', '&igrave;', '&ograve;', '&ugrave;', '&auml;', '&euml;', '&iuml;', '&ouml;', '&uuml;', '&atilde;', '&otilde;', '&ccedil;', '&Aacute;', '&Eacute;', '&Iacute;', '&Oacute;', '&Uacute;', '&Acirc;', '&Ecirc;', '&Icirc;', '&Ocirc;', '&Ucirc;', '&Agrave;', '&Egrave;', '&Igrave;', '&Ograve;', '&Ugrave;', '&Auml;', '&Euml;', '&Iuml;', '&Ouml;', '&Uuml;', '&Atilde;', '&Otilde;', '&Ccedil;', '&copy;', '&reg;');
		// $accent = array('�', '�', '�', '�', '�', '�', '�', '�' , '�', '�', '�', '&', ')', '(', '<', '>', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�');
  }

  $strStringNew = str_replace($accent, $latin1, $strString);

  if ($blnQuot) $strStringNew = preg_replace('%(�|�|\"|\x93|\x94|\x147|\x148)%', "&quot;", $strStringNew);


  return $strStringNew;

}

function latin12AccentString($strString, $blnQuot = 0) {
  	$latin1 = array('&amp;', '&lt;', '&gt;', '&#39;', '&#40;', '&#41;', '&iexcl;', '&cent;', '&pound;', '&yen;', '&brvbar;', '&slash;', '&sect;', '&copy;', '&ordf;', '&laquo;', '&not;', '&reg;', '&macr;', '&deg;', '&plusmn;', '&sup2;', '&sup3;', '&acute;', '&micro;', '&para;', '&middot;', '&cedil;', '&sup1;', '&ordm;', '&raquo;', '&frac14;', '&frac12;', '&frac34;', '&iquest;', '&Agrave;', '&Aacute;', '&Acirc;', '&Atilde;', '&Auml;', '&Aring;', '&Ccedil;', '&Egrave;', '&Eacute;', '&Ecirc;', '&Euml;', '&Igrave;', '&Iacute;', '&Icirc;', '&Iuml;', '&ETH;', '&Ntilde;', '&Ograve;', '&Oacute;', '&Ocirc;', '&Otilde;', '&Ouml;', '&times;', '&Oslash;', '&Ugrave;', '&Uacute;', '&Ucirc;', '&Uuml;', '&THORN;', '&szlig;', '&agrave;', '&aacute;', '&acirc;', '&atilde;', '&auml;', '&aelig;', '&ccedil;', '&egrave;', '&eacute;', '&ecirc;', '&euml;', '&igrave;', '&iacute;', '&icirc;', '&iuml;', '&ograve;', '&oacute;', '&ocirc;', '&otilde;', '&ouml;', '&divide;', '&ugrave;', '&uacute;', '&ucirc;', '&uuml;', '&yacute;', '&yuml;');
		$accent = array('&', '<', '>', '\'', '(', ')', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�');

  $strStringNew = str_replace($latin1, $accent, $strString);

  if ($blnQuot) $strStringNew = str_replace('&quot', '"', $strStringNew);

  return $strStringNew;

}

function array2JSON($array, $compress = 1, $base64 = 1) {
  if (is_array($array) && count($array)) {
    // $array = utf8_encode(accentString2Latin1($array, 1));

    $eJSON = json_encode($array);

    if (json_last_error()) {
	    echo "<br />", json_last_error();

			switch(json_last_error()) {
				case JSON_ERROR_DEPTH:
					echo ' - The maximum stack depth has been exceeded';
					break;

				case JSON_ERROR_CTRL_CHAR:
					echo ' - Control character error, possibly incorrectly encoded';
					break;

				case JSON_ERROR_SYNTAX:
					echo ' - Syntax error';
					break;

				case JSON_ERROR_NONE:
					echo ' - No error has occurred';
					break;

				case JSON_ERROR_STATE_MISMATCH:
					echo ' - Invalid or malformed JSON';
					break;

				case JSON_ERROR_UTF8:
					echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
					break;
			}
			echo '<pre>'.print_r($array, 1);
			die();
    }

    if ($compress) $eJSON = gzcompress($eJSON, 9);

    if ($base64) $eJSON = strtr(base64_encode($eJSON), '+/', '-_');

    return $eJSON;
  }
}

function json2Array($eJSON, $html = 1, $uncompress = 1, $base64 = 1) {
  if (!empty($eJSON) && $eJSON) {
    if ($base64) $eJSON = base64_decode(strtr($eJSON, '-_', '+/'));

    // echo "<br /><b>base64_decode</b><br />";
    // var_dump($eJSON);

    if ($uncompress) $eJSON = gzuncompress($eJSON);

    // echo "<br /><b>gzuncompress</b><br />";
    // var_dump($eJSON);

    $array = json_decode($eJSON);

    if ($html) {
      foreach ($array as &$a)
      	$a = str_replace('\'', '\\\'', str_replace('&quot;', '"', html_entity_decode($a, 1)));
    }

    return $array;
  }
}

function microtime_float() {
  list($usec, $sec) = explode(" ", microtime());
  return ((float)$usec + (float)$sec);
}

function strTime($s, $showms=0) {
  $d = intval($s/86400);
  $s -= $d*86400;

  $h = intval($s/3600);
  $s -= $h*3600;

  $m = intval($s/60);
  $s -= $m*60;

  $ms = ($s % 1000) * 1000;

  if ($d) $str = $d.'d ';
  if ($h) $str .= $h.'h ';
  if ($m) $str .= $m.'m ';
  if ($s) $str .= intval($s).'s';
  if ($ms && $showms) $str .= intval($ms).'ms';

  return $str;
}

function is_base64_encoded($data) {
  if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data)) {
    return 1;
  } else {
    return 0;
  }
}

function NormalizeAccent($strString) {

  $arrNormal = explode(",","a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,a,o,c,A,E,I,O,U,A,E,I,O,U,A,E,I,O,U,A,E,I,O,U,A,I,�,a");
  $arrAccent = explode(",","�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�");

  if ($strString > "") {
    $x=0;

    $NormalizeAccent = $strString;

    $NormalizeAccent = str_replace(chr(170),"a",$NormalizeAccent);

    foreach ($arrAccent as $i) {
      $NormalizeAccent = str_replace($arrAccent[$x],$arrNormal[$x],$NormalizeAccent);

      $x = $x+1;

    }
  }

  return $NormalizeAccent;
}

function dateHumanized($date) {
	$dateFrom = strtotime($date);
	$dateTo   = time();

	$difference = DateDiff('s', $dateFrom, $dateTo, 1);

	$periodos = array('seg', 'min', 'hora', 'dia', 'semana', 'm�s', 'ano');
	// arrTamanhos = array(60, 60, 24, 7, 4.35, 12, 10);
	$diff = array('s', 'n', 'h', 'd', 'w', 'm', 'yyyy');

	if ($difference > 0) {
	  $final = ' atr�s';

	} else {
	  $difference = -$difference;
	  $final = ' por vir';

	}

	$j = 0;

	while ($difference > 0 && $j <= 7) {
	  $differenceLast = $difference;

	  $periodoLast = $periodo;

	  $periodo = $periodos[$j];

	  $difference = DateDiff($diff[$j], $dateFrom, $dateTo, 1);

	  $j++;

	}

	/*
	echo "<hr /><br />&nbsp;<br />DateDiff=".date("Y/m/d H:i:s", DateDiff('s', $dateFrom, $dateTo, 1));
	echo "<br />dateFrom=".date("Y/m/d H:i:s", $dateFrom);
	echo "<br />dateTo=".date("Y/m/d H:i:s", $dateTo);

	echo "<hr />periodo=$periodo";
	echo "<br />periodoLast=$periodoLast";
	echo "<br />difference=$difference";
	echo "<br />differenceLast=$differenceLast";
	echo "<br />dateFrom=$dateFrom";
	echo "<br />dateTo=$dateTo";
	*/

	if ($differenceLast != 1) $periodoLast .= 's';

	if ($periodoLast === 'm�ss') $periodoLast = 'meses';

	return intval($differenceLast).' '.$periodoLast.$final;

}

function DateDiff($interval, $datefrom, $dateto, $using_timestamps = false) {
    /*
    $interval can be:
    yyyy - Number of full years
    q - Number of full quarters
    m - Number of full months
    y - Difference between day numbers
        (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
    d - Number of full days
    w - Number of full weekdays
    ww - Number of full weeks
    h - Number of full hours
    n - Number of full minutes
    s - Number of full seconds (default)
    */

    if (!$using_timestamps) {
        $datefrom = strtotime($datefrom, 0);
        $dateto = strtotime($dateto, 0);
    }
    $difference = $dateto - $datefrom; // Difference in seconds

    switch($interval) {

    case 'yyyy': // Number of full years
        $years_difference = floor($difference / 31536000);
        if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
            $years_difference--;
        }
        if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
            $years_difference++;
        }
        $datediff = $years_difference;
        break;

    case "q": // Number of full quarters

        $quarters_difference = floor($difference / 8035200);
        while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
            $months_difference++;
        }
        $quarters_difference--;
        $datediff = $quarters_difference;
        break;

    case "m": // Number of full months

        $months_difference = floor($difference / 2678400);
        while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
            $months_difference++;
        }
        $months_difference--;
        $datediff = $months_difference;
        break;

    case 'y': // Difference between day numbers

        $datediff = date("z", $dateto) - date("z", $datefrom);
        break;

    case "d": // Number of full days

        $datediff = floor($difference / 86400);
        break;

    case "w": // Number of full weekdays

        $days_difference = floor($difference / 86400);
        // $weeks_difference = floor($days_difference / 7); // Complete weeks

        if (abs($days_difference) >= 7) {
          $weeks_difference = floor($days_difference / 7);
        } else {
          $weeks_difference = 0;
        }

        $first_day = date("w", $datefrom);
        $days_remainder = floor($days_difference % 7);
        $odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
        if ($odd_days > 7) { // Sunday
            $days_remainder--;
        }
        if ($odd_days > 6) { // Saturday
            $days_remainder--;
        }
        $datediff = ($weeks_difference * 5) + $days_remainder;
        break;

    case "ww": // Number of full weeks

        $datediff = floor($difference / 604800);
        break;

    case "h": // Number of full hours

        $datediff = floor($difference / 3600);
        break;

    case "n": // Number of full minutes

        $datediff = floor($difference / 60);
        break;

    default: // Number of full seconds (default)

        $datediff = $difference;
        break;
    }

    return $datediff;

}

function returnCalendar($year, $month, $day, $weekContent, $dayContent, $monthContent, $ul) {
	$daysInMonth = date("t",mktime(0,0,0,$month,1,$year));

	$firstDay = date("w", mktime(0,0,0,$month,1,$year)) - 1;

	$dayMonth = array();

	$days = ceil(($firstDay + $daysInMonth) / 7) * 7 + 7;

	if ($days > 42) $days = 42;

	if ($class = $ul['class']) $class = " class=\"$class\"";

	$result = "<ul$class>";

	$caption = $monthContent['caption'];

	if ($title = $monthContent['title']) $title = " title=\"$title\"";
	if ($class = $monthContent['class']) $class = " class=\"$class\"";
	if ($target = $monthContent['target']) $target = " target=\"$target\"";

	if ($href = $monthContent['href']) $caption = "<a href=\"$href\"$class$target$title>$caption</a>";

	$result .= "<li$class>$caption</li>";

	for($i = 0; $i < 7; $i++) {
	  $caption = $weekContent[$i]['caption'];

	  if ($title = $weekContent[$i]['title']) $title = " title=\"$title\"";
	  if ($class = $weekContent[$i]['class']) $class = " class=\"$class\"";
	  if ($target = $weekContent[$i]['target']) $target = " target=\"$target\"";

	  if ($href = $weekContent[$i]['href']) $caption = "<a href=\"$href\"$class$target>$caption</a>";

	  $result .= "<li$class$title>$caption</li>";
	}

	for($i = 0; $i < $days; $i++) {
	  if (($caption = ($i - $firstDay)) > 0 && $caption <= $daysInMonth) {
	    if (is_array($dayContent[$caption])) $dayMonth[$i] = $dayContent[$caption];

	    $dayMonth[$i]['caption'] = $caption;

	  } else {
	    $dayMonth[$i]['class'] = 'empty';
	  }

	  $caption = $dayMonth[$i]['caption'];

	  if ($title = $dayMonth[$i]['title']) $title = " title=\"$title\"";
	  if ($class = $dayMonth[$i]['class']) $class = " class=\"$class\"";
	  if ($target = $dayMonth[$i]['target']) $target = " target=\"$target\"";

	  if ($href = $dayMonth[$i]['href']) {
	  	$caption = "<a href=\"$href\"$target$title>$caption</a>";
	  } else {
	  	$class = " class=\"empty\"";
	  }

	  $result .= "<li$class>$caption</li>";
	}

	$result .= "</ul>";

	return $result;

}

// ----------------------------------------------------------------
// Aplica o TidyHTML no conte�do
// ================================================================
function tidyApply($strContent) {

 	$arrOptions = array("drop-proprietary-attributes" => true,
           "drop-font-tags" => true,
           "drop-empty-paras" => true,
 					 "fix-backslash" => true,
           "hide-comments" => true,
           "join-classes" => true,
           "join-styles" => true,
           "word-2000" => true,
           "output-xhtml" => true,
           "clean" => true,
           "indent" => false,
           "indent-spaces" => 0,
           "logical-emphasis" => true,
           "lower-literals" => true,
  		   	 "quote-ampersand" => true,
           "quote-marks" => true,
  		     "quote-nbsp" => true,
           "wrap" => 0,
           "show-body-only" => true,
 	         "merge-divs" => "auto",
 	         "merge-spans" => "auto");

 	// ascii-chars
  // utf8

  $objTidy = tidy_parse_string($strContent, $arrOptions);

  $objTidy->cleanRepair();

  $strResult = str_replace("\r\n", "", $objTidy);

  return $strResult;

}

function array2Param($tag, $array) {
	foreach ($array as $key => $value) {
		$param .= "<$tag";

		if ($key > 0) $param .= $key;

		foreach ($value as $keyP => $valueP) {
			$param .= " $keyP=\"$valueP\"";

		}
		$param .= " />";
	}
	return $param;
}

 function rrmdir($dir) {
   if (is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
       }
     }
     reset($objects);
     rmdir($dir);
   }
 }