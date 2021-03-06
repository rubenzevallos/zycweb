<?php
/**
 * zvar
 *
 * Processa as tags para gest�o das vari�veis do sistema.
 *
 * @package ztag
 * @subpackage var
 * @category Environment
 * @version 1.0
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

define("zvarVersion", 1.0, 1);
define("zvarVersionSufix", "ALFA 0.1", 1);

/**
 * Just to check if zTag was loaded
 *
 * <code>
 * zvar_exist();
 * </code>
 *
 * @return boolean 1 if exist
 *
 * @since 1.0
 */
function zvar_exist() {
  return 1;
}

/**
 * Return this zTag version
 *
 * <code>
 * zvar_version();
 * </code>
 *
 * @return string with zTag version
 *
 * @since 1.0
 */
function zvar_version() {
  return zvarVersion." ".zvarVersionSufix;
}

/**
 * Compare the version parameter with current version
 *
 * <code>
 * zvar_compare();
 * </code>
 *
 * @param number $version the version to compare
 *
 * @return boolean true if match with current version
 *
 * @since 1.0
 */
function zvar_compare($version) {
  return zvarVersion === $version;
}

/**
 * Main zTag functions selector
 *
 * <code>
 * zvar_execute($tagId, $tagFunction, $arrayTag, $arrayTagId, $arrayOrder);
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
function zvar_zexecute($tagId, $tagFunction, &$arrayTag, &$arrayTagId, $arrayOrder) {
  $arrParam = $arrayTag[$tagId][ztagParam];

  $strId    = $arrParam["id"];
  $strUse   = $arrParam["use"];
  $strVar   = $arrParam["var"];
  $strValue = $arrParam["value"];

  $strTransform = $arrParam["transform"];

  if ($arrayTag[$tagId][ztagContentWidth]) $strContent = $arrayTag[$tagId][ztagContent];

  $errorMessage = "";

  switch (strtolower($tagFunction)) {
    /*+
     * Creates a $var with it's value
     *
     * <code>
     * <zvar:set id="someVar" value="someValue" />
     *
     * <zvar:set id="otherVar">
     *   It's value
     * </zvar:set>
     * </code>
     *
     * @param string id="someVar" Unique Id for the new variable $varName
     * @param string value="someValue" Value to b saved into this variable
     */
  	case "set":
      if ($arrayTag[$tagId][ztagContentWidth]) {
      	$strValue = ztagVars($arrayTag[$tagId][ztagContent], $arrayTagId);

      	$arrParam["value"] = $strValue;
      }

      if (strlen($strVar)) {
        $strId = $strVar;

        $arrParam["id"] = $strId;
      }

      $errorMessage .= ztagParamCheck($arrParam, "value,id");

      if (strlen($strTransform)) $strValue = ztagTransform($strValue, $strTransform);

      zTagSetVar($arrayTagId, $strId, $strValue);

    	// $arrayTagId["$".$strId][ztagIdValue] = $strValue;
      // $arrayTagId["$".$strId][ztagIdLength] = strlen($strValue);

    	// $arrayTagId["$".$strId][ztagIdType] = idTypeFVar;
      break;

    /*+
     * Creates an array var $someArray with it�s formated content
     *
     * <code>
     * <zvar:setarray id="someArray">
     *   "index"="value"
     *   , "index2":"value 2"
     *   , 'index3'=>'value 3'
     *   , 99="value"
     *   , "indexn"=99
     * </znosql:setarray>
     *
     * <zvar:setarray id="someArray" value="'index'='value',9='value'" />
     * </code>
     *
     * @param string id="someArray" Unique Id for the new variable $varName
     */
    case "setarray":
      if (strlen($strContent)) $contentArray = $strContent;

      if (strlen($strValue)) $contentArray = $strValue;

      if ($contentArray) {
        $contentArray = ztagVars($contentArray, $arrayTagId);

        $arrParam["value"] = $strValue;
      }

      if (!strlen($strContent) && !strlen($strValue)) $errorMessage .= ztagParamCheck($arrParam, "id,value");

      if ($strTransform && $contentArray) $contentArray = ztagTransform($contentArray, $strTransform);

      $contentArray = ltrim($contentArray, "\r\n");
      $contentArray = rtrim($contentArray, "\r\n");

      preg_match_all('%\s*,?\s*(?P<index>"[^"]*?"|\'[^\']*?\'|\d+)\s*(:|=|=>)\s*(?P<value>"[^"\\\\]*(?:\\\\.[^"\\\\]*)*"|\'[^\'\\\\]*(?:\\\\.[^\'\\\\]*)*\'|\d+)%', $contentArray, $Matches, PREG_OFFSET_CAPTURE);

      $arrayContent = array();

      foreach ($Matches[0] as $key => $value) {
        $paramKey   = $Matches["index"][$key][0];
        $paramValue = $Matches["value"][$key][0];

        $patternString = '%^(["\'])(.*?)\1%';

        $paramKey   = preg_replace($patternString, "$2", $paramKey);
        $paramValue = preg_replace($patternString, "$2", $paramValue);

        $arrayContent[$paramKey] = preg_replace('%\\\\([\'"])%', "$1", $paramValue);
      }

      zTagSetVar($arrayTagId, $strId, $arrayContent);

      break;

    /*+
     * Creates a $var if the contidion is made with it�s value
     *
     * <code>
     * <zvar:setif use="getFiltro" equal="nome" var="sqlSelect" then="(SELECT CD_PESSOA FROM TB_ENDERECO_PESSOA WHERE NM_PESSOA LIKE '%$getFiltro%')" />
     *
     * <zvar:setif use="CO_CPF" condition="empty()" var="sqlSelect" then="CO_CPF LIKE '%$CO_CPF%'" />
     * </code>
     *
     * @param string use="getFiltro"
     * @param string equal="nome"
     * @param string var="sqlSelect"
     * @param string then="(SELECT CD_PESSOA FROM TB_ENDERECO_PESSOA WHERE NM_PESSOA LIKE '%$getFiltro%')"
     */
    case "setif":
      $strEqual        = $arrParam["equal"];
      $strNotEqual     = $arrParam["notequal"];
      $strThen         = $arrParam["then"];
      $strElse         = $arrParam["else"];
      $strCondition    = $arrParam["condition"];
      $strNotCondition = $arrParam["notcondition"];

    	$errorMessage .= ztagParamCheck($arrParam, "use,then,var");

    	if ($strUse) $strUse = $arrayTagId["$".$strUse][ztagIdValue];

      if ($strEqual) {
      	if ($strUse == $strEqual) {
          $arrayTagId["$".$strVar][ztagIdValue] = $strThen;
        } else {
	        if ($strElse) $arrayTagId["$".$strVar][ztagIdValue] = $strElse;
	      }
      }

      if ($strNotEqual) {
        if ($strUse != $strNotEqual) {
          $arrayTagId["$".$strVar][ztagIdValue] = $strThen;
        } else {
          if ($strElse) $arrayTagId["$".$strVar][ztagIdValue] = $strElse;
        }
      }

      if ($strCondition) {
      	$strCondition = ztagTransform($strUse, $strCondition);

        if ($strCondition) {
          $arrayTagId["$".$strVar][ztagIdValue] = $strThen;
        } else {
          if ($strElse) $arrayTagId["$".$strVar][ztagIdValue] = $strElse;
        }
      }

      if ($strNotCondition) {
        $strNotCondition = ztagTransform($strUse, $strNotCondition);

        if (!$strNotCondition) {
          $arrayTagId["$".$strVar][ztagIdValue] = $strThen;
        } else {
          if ($strElse) $arrayTagId["$".$strVar][ztagIdValue] = $strElse;
        }
      }
      break;

    /*+
     * Unset a variable
     *
     * <code>
     * <zvar:unset use="getFiltro" />
     * </code>
     *
     * @param string use="getFiltro"
     */

    case "unset":

    /*+
     * Reset a variable
     *
     * <code>
     * <zvar:reset use="getFiltro" />
     * </code>
     *
     * @param string use="getFiltro"
     */
    case "reset":
      $errorMessage = ztagParamCheck($arrParam, "use");

    	if ($arrayTagId["$".$strUse][ztagIdType] != idTypeFVar) $errorMessage .= "<br />The handle \"$strUse\" is not a var one!";

      $arrayTagId["$".$strUse] = array();
      break;

    /*+
     * Update a $var with it's value
     *
     * <code>
     * <zvar:update use="someVar" value="someValue" />
     *
     * <zvar:set use="otherVar">
     *   It's value
     * </zvar:set>
     * </code>
     *
     * @param string use="someVar" Unique Id of a variable $varName
     * @param string value="someValue" Value to b saved into this variable
     */
    case "update":
      if ($arrayTag[$tagId][ztagContentWidth]) {
        $strValue = ztagVars($arrayTag[$tagId][ztagContent], $arrayTagId);

        $arrParam["value"] = $strValue;
      }

      $errorMessage .= ztagParamCheck($arrParam, "value,use");

      if (strlen($strTransform)) $strValue = ztagTransform($strValue, $strTransform);
      
      zTagSetVar($arrayTagId, $strUse, $strValue);
      break;

    /*+
     * Get and return a variable value
     *
     * <code>
     * <zvar:get use="getFiltro" />
     * </code>
     *
     * @param string use="getFiltro"
     */
    case "get":

    /*+
     * Show variable value
     *
     * <code>
     * <zvar:show use="getFiltro" />
     * </code>
     *
     * @param string use="getFiltro"
     */
    case "show":
      $errorMessage .= ztagParamCheck($arrParam, "use");

      preg_match_all('%^(?P<var>\w+)\\[(?P<index>[^\\]]+)\\]$%', $strUse, $Matches, PREG_OFFSET_CAPTURE);

      if (count($Matches[0][0])) {
        $var   = $Matches["var"][0][0];
        $index = $Matches["index"][0][0];

        $varValue = $arrayTagId["$".$var][ztagIdValue];

        $varValue = $varValue[$index];

      } else {
        if ($arrayTagId["$".$strUse][ztagIdType] != idTypeFVar) $errorMessage .= "<br />The handle \"$strUse\" is not a var one!";

        $varValue = $arrayTagId["$".$strUse][ztagIdValue];
        
      }

      if (strlen($strTransform)) $varValue = ztagTransform($varValue, $strTransform);

      $arrayTag[$tagId][ztagResult] = $varValue;
      break;

    /*+
     * Creates a $arrayVar for var parameters, applying the pattern using use var content.
     *
     * <code>
     * <zvar:regex pattern='%title="(?P<title>.*?)" href="(?P<url>/\d+/\d+_\w+/(?P<id>\d+)/[\w-]+.htm)%' use="urlAssunto" var="arrayAssunto" />
     * </code>
     *
     * @param string pattern='%title="(?P<title>.*?)" href="(?P<url>/\d+/\d+_\w+/(?P<id>\d+)/[\w-]+.htm)%'
     * @param string use="urlAssunto"
     * @param string value="$arrayValue"
     * @param string var="arrayAssunto"
     * @param string error="errorVar"
     */
    case "regex":
      $strPattern = $arrParam["pattern"];
      $strError   = $arrParam["error"];

      $errorMessage .= ztagParamCheck($arrParam, "pattern,var");

      if ($strUse) $strValue = $arrayTagId["$".$strUse][ztagIdValue];

      preg_match_all($strPattern, $strValue, $Matches, PREG_SET_ORDER);

      $arrayResult = array();

      foreach ($Matches as $keyMatches => $valueMatches) {
        foreach ($valueMatches as $key => $value) {
          if (is_string($key) || $key) $arrayMatches[$key] = $value;
        }

        $arrayResult[] = $arrayMatches;
      }

      if (preg_last_error() && strlen($strError)) {
        zTagSetVar($arrayTagId, $strError, preg_last_error());

      } else {
        zTagSetVar($arrayTagId, $strError, array());
      }

      if (strlen($strVar) && count($arrayResult)) {
        zTagSetVar($arrayTagId, $strVar, $arrayResult);

      } else {
        zTagSetVar($arrayTagId, $strVar, array());

      }
      break;

    /*+
     * Decrement a variable value
     *
     * <code>
     * <zvar:decrement use="getFiltro" />
     * </code>
     *
     * @param string use="getFiltro"
     */
    case "decrement":

    /*+
     * Decrement a variable value
     *
     * <code>
     * <zvar:dec use="getFiltro" />
     * </code>
     *
     * @param string use="getFiltro"
     */
    case "dec":
      $errorMessage .= ztagParamCheck($arrParam, "use");

      preg_match_all('%^(?P<var>\w+)\\[(?P<index>[^\\]]+)\\]$%', $strUse, $Matches, PREG_OFFSET_CAPTURE);

      if (count($Matches[0][0])) {
        $var   = $Matches["var"][0][0];
        $index = $Matches["index"][0][0];

        $varValue = $arrayTagId["$".$var][ztagIdValue];

        $arrayTagId["$".$var][ztagIdValue] = $varValue[$index]--;

      } else {
        if ($arrayTagId["$".$strUse][ztagIdType] != idTypeFVar) $errorMessage .= "<br />The handle \"$strUse\" is not a var one!";

        $arrayTagId["$".$strUse][ztagIdValue]--;

      }
      break;

    /*+
     * Increment a variable value
     *
     * <code>
     * <zvar:increment use="getFiltro" />
     * </code>
     *
     * @param string use="getFiltro"
     */
    case "increment":

    /*+
     * Increment a variable value
     *
     * <code>
     * <zvar:inc use="getFiltro" />
     * </code>
     *
     * @param string use="getFiltro"
     */
    case "inc":
      $errorMessage .= ztagParamCheck($arrParam, "use");

      preg_match_all('%^(?P<var>\w+)\\[(?P<index>[^\\]]+)\\]$%', $strUse, $Matches, PREG_OFFSET_CAPTURE);

      if (count($Matches[0][0])) {
        $var   = $Matches["var"][0][0];
        $index = $Matches["index"][0][0];

        $varValue = $arrayTagId["$".$var][ztagIdValue];

        $arrayTagId["$".$var][ztagIdValue] = $varValue[$index]++;

      } else {
        if ($arrayTagId["$".$strUse][ztagIdType] != idTypeFVar) $errorMessage .= "<br />The handle \"$strUse\" is not a var one!";

        $arrayTagId["$".$strUse][ztagIdValue]++;

      }
      break;

    default:
      $errorMessage .= "<br />Undefined function \"$tagFunction\"";

  }

  ztagError($errorMessage, $arrayTag, $tagId);
}
