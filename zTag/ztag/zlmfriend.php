<?php
/**
 * zLMFriend
 *
 * Lugar M�dico - zTags para o processamento dos colegas
 *
 * @package ztag
 * @subpackage header
 * @category Environment
 * @version 1.0
 * @author Ruben Zevallos Jr. <zevallos@zevallos.com.br>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright 2007-2010 by Ruben Zevallos(r) Jr.
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
 * and is licensed under the LGPL. For more information, see
 * <http://ztag.zyc.com.br> *
 */

define("zlmfriendVersion", 1.0, 1);
define("zlmfriendVersionSufix", "ALFA 0.1", 1);

/**
 * Just to check if zTag was loaded
 *
 * <code>
 * zlmfriend_exist();
 * </code>
 *
 * @return boolean 1 if exist
 *
 * @since 1.0
 */
function zlmfriend_exist() {
  return 1;
}

/**
 * Return this zTag version
 *
 * <code>
 * zlmfriend_version();
 * </code>
 *
 * @return string with zTag version
 *
 * @since 1.0
 */
function zlmfriend_version() {
  return zlmfriendVersion." ".zlmfriendVersionSufix;
}

/**
 * Compare the version parameter with current version
 *
 * <code>
 * zlmfriend_compare();
 * </code>
 *
 * @param number $version the version to compare
 *
 * @return boolean true if match with current version
 *
 * @since 1.0
 */
function zlmfriend_compare($version) {
  return zlmfriendVersion === $version;
}

/**
 * Main zTag functions selector
 *
 * <code>
 * zlmfriend_execute($tagId, $tagFunction, $arrayTag, $arrayTagId, $arrayOrder);
 * </code>
 *
 * @param integer $tagId array id of current zTag of $arrayTag array
 * @param string $tagFunction name of zTag function
 * @param array $arrayTag array with all compiled zTags
 * @param array $arrayTagId array with all Ids values
 * @param array $arrayOrder array with zTag executing order
 *
 * @since 1.0
 */
function zlmfriend_zexecute($tagId, $tagFunction, &$arrayTag, &$arrayTagId, $arrayOrder) {
  $arrParam = $arrayTag[$tagId][ztagParam];

  $strVar   = $arrParam["var"];
  $strUser  = $arrParam["user"];
  $strUse   = $arrParam["use"];
  
  $errorMessage = "";

  $tagValue = $arrayTagId[$strParam][ztagIdValue];

  switch (strtolower($tagFunction)) {
    /*+
     * Return an array with all user friends
     *
     * <code>
     * <zlmfriend:friendlist use="myConn" user="!userLogged" var="friendList" />
     * </code>
     *
     * @param string use="myConn" Database connection 
     * @param string user="!userLogged" User that you want to get his friend list 
     * @param string var="friendList" Variable to save the array
     *
     * @author Ruben Zevallos Jr. <zevallos@zevallos.com.br>
     */
    case "circlelist":
  		$errorMessage .= ztagParamCheck($arrParam, "use,var,user");
  		
    	$sql = "SELECT P.CD_PESSOA id
    	, P.NM_PESSOA nome
    	FROM TB_PESSOA P
    	LEFT JOIN TB_COLEGA Co ON P.CD_PESSOA = Co.CD_PESSOA_COLEGA
    	WHERE Co.FL_ATIVO = 1 AND Co.CD_PESSOA = ".$strUser;

      $dbHandle = $arrayTagId[$strUse][ztagIdHandle];
    	
			dbQuery($dbHandle, $sql);
			
			$i=0;
			
  		while (dbFetch($dbHandle, $strResultType)) {
  			$arrayVar[$i++] = $dbHandle[dbHandleFetch];
  			
  		}

  		zTagSetVar($arrayTagId, $strVar, $arrayVar);
    	break;

    /*+
     * Return an array with user profile
     *
     * <code>
     * <zlmfriend:profile use="myConn" user="!userLogged" var="profile" />
     * </code>
     *
     * @param string use="myConn" Database connection 
     * @param string user="!userLogged" User that you want to get his friend list 
     * @param string var="friendList" Variable to save the array
     *
     * @author Ruben Zevallos Jr. <zevallos@zevallos.com.br>
     */
    case "profile":
  		$errorMessage .= ztagParamCheck($arrParam, "use,var,user");
  		
    	$sql = "SELECT P.CD_PESSOA id
    	, P.NM_PESSOA nome
    	, P.NM_PRIMEIRO primeiro
    	, P.NM_ULTIMO ultimo
    	, P.DS_EMAIL email
    	, P.DS_AVATAR avatar
    	, DATE_FORMAT(P.DT_INCLUSAO , '%d/%m/%Y %H:%i:%S') inclusao
    	, DATE_FORMAT(P.DT_INCLUSAO , '%d') inclusao_day
    	, DATE_FORMAT(P.DT_INCLUSAO , '%m') inclusao_month
    	, DATE_FORMAT(P.DT_INCLUSAO , '%Y') inclusao_year
    	, DATE_FORMAT(P.DT_INCLUSAO , '%H') inclusao_hour
    	, DATE_FORMAT(P.DT_INCLUSAO , '%i') inclusao_minute
    	, DATE_FORMAT(P.DT_INCLUSAO , '%S') inclusao_second    	
    	FROM TB_PESSOA P
    	WHERE P.CD_PESSOA = ".$strUser;

      $dbHandle = $arrayTagId[$strUse][ztagIdHandle];
    	
			dbQuery($dbHandle, $sql);
						
  		if (dbFetch($dbHandle, $strResultType)) {
  			zTagSetVar($arrayTagId, $strVar, $dbHandle[dbHandleFetch]);
  			
  		}	
    	break;

    /*+
     * Return an array with all circle data
     *
     * <code>
     * <zlmfriend:cirle use="myConn" circle="!userLogged" var="circle" />
     * </code>
     *
     * @param string use="myConn" Database connection 
     * @param string user="!userLogged" User that you want to get his friend list 
     * @param string var="friendList" Variable to save the array
     *
     * @author Ruben Zevallos Jr. <zevallos@zevallos.com.br>
     */
    case "cirle":
  		$errorMessage .= ztagParamCheck($arrParam, "use,var,circle");
  		
    	$sql = "SELECT Ci.CD_CIRCULO id
    	, Ci.NM_CIRCULO nome
    	, DATE_FORMAT(Ci.DT_INCLUSAO , '%d/%m/%Y %H:%i:%S') inclusao
    	, DATE_FORMAT(Ci.DT_INCLUSAO , '%d') inclusao_day
    	, DATE_FORMAT(Ci.DT_INCLUSAO , '%m') inclusao_month
    	, DATE_FORMAT(Ci.DT_INCLUSAO , '%Y') inclusao_year
    	, DATE_FORMAT(Ci.DT_INCLUSAO , '%H') inclusao_hour
    	, DATE_FORMAT(Ci.DT_INCLUSAO , '%i') inclusao_minute
    	, DATE_FORMAT(Ci.DT_INCLUSAO , '%S') inclusao_second    	
    	FROM TB_CIRCULO Ci
    	WHERE Ci.FL_ATIVO = 1 AND Ci.CD_CIRCULO = ".$strCircle;

      $dbHandle = $arrayTagId[$strUse][ztagIdHandle];
    	
			dbQuery($dbHandle, $sql);
			
  		if (dbFetch($dbHandle, $strResultType)) {
  			zTagSetVar($arrayTagId, $strVar, $dbHandle[dbHandleFetch]);
  			
  		}	
			break;
    	
		/*+
     * Return an array with all user friends
     *
     * <code>
     * <zlmfriend:cirles use="myConn" user="!userLogged" var="friendList" />
     * </code>
     *
     * @param string use="myConn" Database connection 
     * @param string user="!userLogged" User that you want to get his friend list 
     * @param string var="friendList" Variable to save the array
     *
     * @author Ruben Zevallos Jr. <zevallos@zevallos.com.br>
     */
    case "cirles":
  		$errorMessage .= ztagParamCheck($arrParam, "use,var,user");
  		
    	$sql = "SELECT Ci.CD_CIRCULO id
    	, Ci.NM_CIRCULO nome
    	, DATE_FORMAT(Ci.DT_INCLUSAO , '%d/%m/%Y %H:%i:%S') inclusao
    	, DATE_FORMAT(Ci.DT_INCLUSAO , '%d') inclusao_day
    	, DATE_FORMAT(Ci.DT_INCLUSAO , '%m') inclusao_month
    	, DATE_FORMAT(Ci.DT_INCLUSAO , '%Y') inclusao_year
    	, DATE_FORMAT(Ci.DT_INCLUSAO , '%H') inclusao_hour
    	, DATE_FORMAT(Ci.DT_INCLUSAO , '%i') inclusao_minute
    	, DATE_FORMAT(Ci.DT_INCLUSAO , '%S') inclusao_second
    	, (SELECT COUNT(*) FROM TB_CIRCULO_PESSOA CP WHERE CP.CD_CIRCULO = Ci.CD_CIRCULO AND CP.FL_ATIVO = 1) qtd_pessoa    	
    	FROM TB_CIRCULO Ci
    	WHERE Ci.FL_ATIVO = 1 AND Ci.CD_PESSOA = ".$strUser;

      $dbHandle = $arrayTagId[$strUse][ztagIdHandle];
    	
			dbQuery($dbHandle, $sql);
			
			$i=0;
			
  		while (dbFetch($dbHandle, $strResultType)) {
  			$arrayVar[$i++] = $dbHandle[dbHandleFetch];
  			
  		}

  		zTagSetVar($arrayTagId, $strVar, $arrayVar);
    	break;

    /*+
     * Return an array with all user friends
     *
     * <code>
     * <zlmfriend:friendlist use="myConn" user="!userLogged" var="friendList" />
     * </code>
     *
     * @param string use="myConn" Database connection 
     * @param string user="!userLogged" User that you want to get his friend list 
     * @param string var="friendList" Variable to save the array
     *
     * @author Ruben Zevallos Jr. <zevallos@zevallos.com.br>
     */
    case "circlelist":
  		$errorMessage .= ztagParamCheck($arrParam, "use,var,circle");
  		
  		if ($strCircle) {
  			$strCircleSQL = "AND CD_CIRCULO = $strCircle";
  		} else if ($strUser) {
  			$strUserSQL = "AND CD_CIRCULO IN (SELECT CD_CIRCULO FROM TB_CIRCULO WHERE FL_ATIVO = 1 AND CD_PESSOA = $strUser)";
  		}
  		
    	$sql = "SELECT P.CD_PESSOA id
    	, P.NM_PESSOA nome
	    , CASE WHEN P.DS_AVATAR IS NULL THEN '/avatar/avatar.jpg' ELSE P.DS_AVATAR END avatar
	    , CASE WHEN P.DS_URL_PERFIL IS NULL THEN CONCAT('/perfil.ztag?p=', P.CD_PESSOA) ELSE P.DS_URL_PERFIL END profileurl
    	FROM TB_PESSOA P
    	WHERE P.FL_ATIVO = 1 AND P.CD_PESSOA IN (
    	SELECT DISTINCT CD_PESSOA 
    	  FROM TB_CIRCULO_PESSOA 
    	  WHERE FL_ATIVO $strCircleSQL$strUserSQL
    	)";
    	
    	// echo "<br /><pre>".$sql;

      $dbHandle = $arrayTagId[$strUse][ztagIdHandle];
    	
			dbQuery($dbHandle, $sql);
			
			$i=0;
			
  		while (dbFetch($dbHandle, $strResultType)) {
  			$arrayVar[$i++] = $dbHandle[dbHandleFetch];
  			
  		}

  		zTagSetVar($arrayTagId, $strVar, $arrayVar);
    	break;
    	
    default:
      $errorMessage .= "<br />Undefined function \"$tagFunction\"";

  }

  /*
  debugError("tagFunction=$tagFunction"
            ."<br />tagId=$tagId"
            ."<br />strId=$strId"
            ."<br />strUse=$strUse"
            ."<br />strValue=$strValue"
            ."<br />arrayTagId[$strId][ztagIdValue]=".$arrayTagId[$strId][ztagIdValue]
            ."<br />arrayTagId[$strUse][ztagIdValue]=".$arrayTagId[$strUse][ztagIdValue]
            ."<br />arrayTag[$tagId][ztagResult]=".$arrayTag[$tagId][ztagResult]);
            */

  ztagError($errorMessage, $arrayTag, $tagId);
}
