<?php
/**
 * zTag config file
 *
 * Have all config info for zTag to work
 *
 * @package ztag
 * @subpackage config
 * @version 4.0
 * @author Ruben Zevallos Jr. <zevallos@zevallos.com.br>
 * @license http://www.gnu.org/licenses/gpl.html - GNU Public License
 * @copyright 2010 by Ruben Zevallos(r) Jr.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the GNU GPL. For more information, see
 * <http://http://code.google.com/p/ztag/>
 */

error_reporting(E_ALL & ~E_NOTICE);

set_time_limit(10000);

/**
 * Folder of all DB driver files
 */
define('dbFolder', zTagRootDir.'/db'); // DB drives folder

session_cache_limiter('zTag');
$sintCacheLimiter = session_cache_limiter();

session_cache_expire(20);
$sintCacheExpire = session_cache_expire();

session_start();

$sstrSessionID = session_id();

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Cache-Control: no-cache, no-store, max-age=0, must-revalidate");
header("Pragma: no-cache");

if (get_magic_quotes_gpc()) {
  $_REQUEST = array_map('stripslashes', $_REQUEST);
  $_GET = array_map('stripslashes', $_GET);
  $_POST = array_map('stripslashes', $_POST);
  $_COOKIE = array_map('stripslashes', $_COOKIE);
}

$sstrScriptName = basename($_SERVER["PHP_SELF"]);
define('ScriptName', $sstrScriptName);

$_REQUEST = array_change_key_case($_REQUEST, CASE_LOWER);

$sparOption = intval($_REQUEST["o"]);
define('parOption', $sparOption);

$sparAction = intval($_REQUEST["a"]);
define('parAction', $sparAction);

$sparTarget = $_REQUEST["t"];
define('parTarget', $sparTarget);

$sparType   = intval($_REQUEST["y"]);
define('parType', $sparType);

$sparPrint = intval($_REQUEST["p"]);
define('parPrint', $sparPrint);

$sparWhere = $_REQUEST["w"];
define('parWhere', $sparWhere);

$sparDateBegin = $_REQUEST["db"];
define('parDateBegin', $sparDateBegin);

$sparDateEnd = $_REQUEST["de"];
define('parDateEnd', $sparDateEnd);

if (strlen($_REQUEST["debugfile"])) $spardebugFile = $_REQUEST["debugfile"];
define('debugFile', $spardebugFile);

if (strlen($_REQUEST["debugfunction"])) $spardebugFunction = $_REQUEST["debugfunction"];
define('debugFunction', $spardebugFunction);

if (strlen($_REQUEST["debuglevel"])) $spardebugLevel= $_REQUEST["debuglevel"];
define('debugLevel', $spardebugLevel);

if (strlen($_REQUEST["debug"])) {
  $sparDebug         = $_REQUEST["debug"];
  $_SESSION["debug"] = $sparDebug;
}

if (strlen($_REQUEST["debugon"])) {
  $sparDebugON         = $_REQUEST["debugon"];
  $_SESSION["debugON"] = $sparDebugON;
}

if ($sparDebugON) $_SESSION["debugON"] = $sparDebugON;

if ($sparDebugON && $sparDebug) $_SESSION["debug"] = $sparDebug;

if ($_SESSION["debugON"] && $_SESSION["debug"]) $sparDebug = $_SESSION["debug"];

define('parDebug', $sparDebug);

// define('parDebug', 1);

$sparTemplateFile = $_REQUEST["temp"];
define('templateFile', $sparTemplateFile);

$sbmtButton = $_REQUEST["bmtbutton"];

$sparLog = intval($_REQUEST["l"]);

$sparLog = 1;

$sstrDomain        = strtolower($_SERVER["SERVER_NAME"]);
define('Domain', $sstrDomain);

$sstrRemoteAddress = strtolower($_SERVER["REMOTE_ADDR"]);
define('RemoteAddress', $sstrRemoteAddress);

$sstrRequestMethod = strtolower($_SERVER["REQUEST_METHOD"]);
define('RequestMethod', $sstrRequestMethod);

$sstrHTTPHost      = strtolower($_SERVER["HTTP_HOST"]);
define('HTTPHost', $sstrHTTPHost);

$sstrHTTPReferer   = strtolower($_SERVER["HTTP_REFERER"]);
define('HTTPReferer', $sstrHTTPReferer);

$sstrHTTPUserAgent = strtolower($_SERVER["HTTP_USER_AGENT"]);
define('HTTPUserAgent', $sstrHTTPUserAgent);

$sstrServerPort    = strtolower($_SERVER["SERVER_PORT"]);
define('ServerPort', $sstrServerPort);

// Dados do sistema
define('sysName', 'Lugar M�dico', 1);
define('sysSign', 'LM', 1);

switch (php_uname('n')) {
  case 'ZevallosNote3': // Ubuntu
    // MySQL - Connection
    define('myHost', 'localhost', 1);
    define('myDatabase', 'LugarMedico', 1);
    define('myUser', 'root', 1);
    define('myPassword', 'SuperRuben99', 1);
    break;

  case 'hm4204': // Locaweb
    // MySQL - Connection
    define('myHost', 'mysql03.zyc.com.br', 1);
    define('myDatabase', 'zyc3', 1);
    define('myUser', 'zyc3', 1);
    define('myPassword', 'lm432*', 1);
    break;

  case 'UNIDENTALAPP':
    putenv('ORACLE_HOME=C:\\xampp\\PHP');
    putenv('ORACLE_SID=xe');
    putenv('TNS_ADMIN=C:\\xampp\\PHP');

    define('fbDatabase', '192.168.0.3:D:/Javenessi/Alianca08/BancoDeDados/ALIANCA08_01.FDB', 1);
    define('fbUser', 'SYSDBA', 1);
    define('fbPassword', 'masterkey', 1);

    // connect ztag.d2.net.br:D:/Javenessi/Alianca08/BancoDeDados/ALIANCA08_01.FDB user sysdba password masterkey;

    define('ociUser', 'unidental', 1);
    define('ociPassword', 'Un1dental2010', 1);
    define('ociHost', '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=ztag.d2.net.br)(PORT=1521)))(CONNECT_DATA=(SERVICE_NAME=xe)))', 1);
    break;

  default:
    define('fbDatabase', '192.168.0.3:D:/Javenessi/Alianca08/BancoDeDados/ALIANCA08_01.FDB', 1);
    define('fbUser', 'SYSDBA', 1);
    define('fbPassword', 'masterkey', 1);

    define('ociUser', 'unidental', 1);
    define('ociPassword', 'Un1dental2010', 1);
    define('ociHost', '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=192.168.0.84)(PORT=1521)))(CONNECT_DATA=(SERVICE_NAME=xe)))', 1);

    // MySQL - Connection
    define('myHost', 'localhost', 1);
    define('myDatabase', 'LugarMedico', 1);
    define('myUser', 'lm', 1);
    define('myPassword', 'LM432*', 1);

    // MySQL - Connection
    define('mysqlHost', 'localhost', 1);
    define('mysqlDatabase', 'zDB', 1);
    define('mysqlUser', 'zDB', 1);
    define('mysqlPassword', 'zDB4321$', 1);

    // MSSQL - Connection
    define('mssqlHost', 'sql.direito2.com.br', 1);
    define('mssqlDatabase', 'guesaerrante_com_br', 1);
    define('mssqlUser', 'zDB', 1);
    define('mssqlPassword', 'zDB4321$', 1);

    // MSSQL - Connection - OM - Direito 2
    define('mssqlOMHost', 'sql.direito2.com.br', 1);
    define('mssqlOMDatabase', 'direito2_com_br', 1);
    define('mssqlOMUser', 'zDB', 1);
    define('mssqlOMPassword', 'zDB4321$', 1);

    // MSSQL - Connection - Nativa
    define('nativaHost', 'sg.unidental.com.br', 1);
    define('nativaDatabase', 'UNID', 1);
    define('nativaUser', 'Nativa', 1);
    define('nativaPassword', 'Nativa432$', 1);

    // MySQL - WordPress - D2
    define('mysqlHostWP', 'www.direito2.com.br', 1);
    define('mysqlDatabaseWP', 'cidadesat', 1);
    define('mysqlUserWP', 'root', 1);
    define('mysqlPasswordWP', 'SuperWeZ$', 1);

}

