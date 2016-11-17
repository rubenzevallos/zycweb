<?php # vim:ts=2:sw=2:et:
/* For licensing and copyright terms, see the file named LICENSE */

require_once 'Zend/Search/Lucene.php';

/**
 * Copyright (c) 2005 Richard Heyes (http://www.phpguru.org/)
 * PHP5 Implementation of the Porter Stemmer algorithm. Certain elements
 * were borrowed from the (broken) implementation by Jon Abernathy.
 */
class PorterStemmer {
  /**
   * Regex for matching a consonant
   * @var string
   */
  private static $regex_consonant =
    '(?:[bcdfghjklmnpqrstvwxz]|(?<=[aeiou])y|^y)';

  /**
   * Regex for matching a vowel
   * @var string
   */
  private static $regex_vowel = '(?:[aeiou]|(?<![aeiou])y)';

  /**
   * Stems a word. Simple huh?
   *
   * @param  string $word Word to stem
   * @return string       Stemmed word
   */
  public static function Stem($word)
  {
    if (strlen($word) <= 2) {
      return $word;
    }

    $word = self::step1ab($word);
    $word = self::step1c($word);
    $word = self::step2($word);
    $word = self::step3($word);
    $word = self::step4($word);
    $word = self::step5($word);

    return $word;
  }

  /**
   * Step 1
   */
  private static function step1ab($word)
  {
    // Part a
    if (substr($word, -1) == 's') {

      self::replace($word, 'sses', 'ss')
        OR self::replace($word, 'ies', 'i')
        OR self::replace($word, 'ss', 'ss')
        OR self::replace($word, 's', '');
    }

    // Part b
    if (substr($word, -2, 1) != 'e' OR !self::replace($word, 'eed', 'ee', 0)) { // First rule
      $v = self::$regex_vowel;

      // ing and ed
      if (   preg_match("#$v+#", substr($word, 0, -3)) && self::replace($word, 'ing', '')
          OR preg_match("#$v+#", substr($word, 0, -2)) && self::replace($word, 'ed', '')) { // Note use of && and OR, for precedence reasons

        // If one of above two test successful
        if (    !self::replace($word, 'at', 'ate')
            AND !self::replace($word, 'bl', 'ble')
            AND !self::replace($word, 'iz', 'ize')) {

          // Double consonant ending
          if (    self::doubleConsonant($word)
              AND substr($word, -2) != 'll'
              AND substr($word, -2) != 'ss'
              AND substr($word, -2) != 'zz') {

            $word = substr($word, 0, -1);

          } else if (self::m($word) == 1 AND self::cvc($word)) {
            $word .= 'e';
          }
        }
      }
    }

    return $word;
  }

  /**
   * Step 1c
   *
   * @param string $word Word to stem
   */
  private static function step1c($word)
  {
    $v = self::$regex_vowel;

    if (substr($word, -1) == 'y' && preg_match("#$v+#", substr($word, 0, -1))) {
      self::replace($word, 'y', 'i');
    }

    return $word;
  }

  /**
   * Step 2
   *
   * @param string $word Word to stem
   */
  private static function step2($word)
  {
    switch (substr($word, -2, 1)) {
      case 'a':
        self::replace($word, 'ational', 'ate', 0)
          OR self::replace($word, 'tional', 'tion', 0);
        break;

      case 'c':
        self::replace($word, 'enci', 'ence', 0)
          OR self::replace($word, 'anci', 'ance', 0);
        break;

      case 'e':
        self::replace($word, 'izer', 'ize', 0);
        break;

      case 'g':
        self::replace($word, 'logi', 'log', 0);
        break;

      case 'l':
        self::replace($word, 'entli', 'ent', 0)
          OR self::replace($word, 'ousli', 'ous', 0)
          OR self::replace($word, 'alli', 'al', 0)
          OR self::replace($word, 'bli', 'ble', 0)
          OR self::replace($word, 'eli', 'e', 0);
        break;

      case 'o':
        self::replace($word, 'ization', 'ize', 0)
          OR self::replace($word, 'ation', 'ate', 0)
          OR self::replace($word, 'ator', 'ate', 0);
        break;

      case 's':
        self::replace($word, 'iveness', 'ive', 0)
          OR self::replace($word, 'fulness', 'ful', 0)
          OR self::replace($word, 'ousness', 'ous', 0)
          OR self::replace($word, 'alism', 'al', 0);
        break;

      case 't':
        self::replace($word, 'biliti', 'ble', 0)
          OR self::replace($word, 'aliti', 'al', 0)
          OR self::replace($word, 'iviti', 'ive', 0);
        break;
    }

    return $word;
  }

  /**
   * Step 3
   *
   * @param string $word String to stem
   */
  private static function step3($word)
  {
    switch (substr($word, -2, 1)) {
      case 'a':
        self::replace($word, 'ical', 'ic', 0);
        break;

      case 's':
        self::replace($word, 'ness', '', 0);
        break;

      case 't':
        self::replace($word, 'icate', 'ic', 0)
          OR self::replace($word, 'iciti', 'ic', 0);
        break;

      case 'u':
        self::replace($word, 'ful', '', 0);
        break;

      case 'v':
        self::replace($word, 'ative', '', 0);
        break;

      case 'z':
        self::replace($word, 'alize', 'al', 0);
        break;
    }

    return $word;
  }

  /**
   * Step 4
   *
   * @param string $word Word to stem
   */
  private static function step4($word)
  {
    switch (substr($word, -2, 1)) {
      case 'a':
        self::replace($word, 'al', '', 1);
        break;

      case 'c':
        self::replace($word, 'ance', '', 1)
          OR self::replace($word, 'ence', '', 1);
        break;

      case 'e':
        self::replace($word, 'er', '', 1);
        break;

      case 'i':
        self::replace($word, 'ic', '', 1);
        break;

      case 'l':
        self::replace($word, 'able', '', 1)
          OR self::replace($word, 'ible', '', 1);
        break;

      case 'n':
        self::replace($word, 'ant', '', 1)
          OR self::replace($word, 'ement', '', 1)
          OR self::replace($word, 'ment', '', 1)
          OR self::replace($word, 'ent', '', 1);
        break;

      case 'o':
        if (substr($word, -4) == 'tion' OR substr($word, -4) == 'sion') {
          self::replace($word, 'ion', '', 1);
        } else {
          self::replace($word, 'ou', '', 1);
        }
        break;

      case 's':
        self::replace($word, 'ism', '', 1);
        break;

      case 't':
        self::replace($word, 'ate', '', 1)
          OR self::replace($word, 'iti', '', 1);
        break;

      case 'u':
        self::replace($word, 'ous', '', 1);
        break;

      case 'v':
        self::replace($word, 'ive', '', 1);
        break;

      case 'z':
        self::replace($word, 'ize', '', 1);
        break;
    }

    return $word;
  }

  /**
   * Step 5
   *
   * @param string $word Word to stem
   */
  private static function step5($word)
  {
    // Part a
    if (substr($word, -1) == 'e') {
      if (self::m(substr($word, 0, -1)) > 1) {
        self::replace($word, 'e', '');

      } else if (self::m(substr($word, 0, -1)) == 1) {

        if (!self::cvc(substr($word, 0, -1))) {
          self::replace($word, 'e', '');
        }
      }
    }

    // Part b
    if (self::m($word) > 1 AND
        self::doubleConsonant($word) AND substr($word, -1) == 'l') {
      $word = substr($word, 0, -1);
    }

    return $word;
  }

  /**
   * Replaces the first string with the second, at the end of the string. If third
   * arg is given, then the preceding string must match that m count at least.
   *
   * @param  string $str   String to check
   * @param  string $check Ending to check for
   * @param  string $repl  Replacement string
   * @param  int    $m     Optional minimum number of m() to meet
   * @return bool          Whether the $check string was at the end
   *                       of the $str string. True does not necessarily mean
   *                       that it was replaced.
   */
  private static function replace(&$str, $check, $repl, $m = null)
  {
    $len = 0 - strlen($check);

    if (substr($str, $len) == $check) {
      $substr = substr($str, 0, $len);
      if (is_null($m) OR self::m($substr) > $m) {
        $str = $substr . $repl;
      }

      return true;
    }

    return false;
  }

  /**
   * What, you mean it's not obvious from the name?
   *
   * m() measures the number of consonant sequences in $str. if c is
   * a consonant sequence and v a vowel sequence, and <..> indicates arbitrary
   * presence,
   *
   * <c><v>       gives 0
   * <c>vc<v>     gives 1
   * <c>vcvc<v>   gives 2
   * <c>vcvcvc<v> gives 3
   *
   * @param  string $str The string to return the m count for
   * @return int         The m count
   */
  private static function m($str)
  {
    $c = self::$regex_consonant;
    $v = self::$regex_vowel;

    $str = preg_replace("#^$c+#", '', $str);
    $str = preg_replace("#$v+$#", '', $str);

    preg_match_all("#($v+$c+)#", $str, $matches);

    return count($matches[1]);
  }


  /**
   * Returns true/false as to whether the given string contains two
   * of the same consonant next to each other at the end of the string.
   *
   * @param  string $str String to check
   * @return bool        Result
   */
  private static function doubleConsonant($str)
  {
    $c = self::$regex_consonant;

    return preg_match("#$c{2}$#", $str, $matches)
      AND $matches[0]{0} == $matches[0]{1};
  }


  /**
   * Checks for ending CVC sequence where second C is not W, X or Y
   *
   * @param  string $str String to check
   * @return bool        Result
   */
  private static function cvc($str)
  {
    $c = self::$regex_consonant;
    $v = self::$regex_vowel;

    return     preg_match("#($c$v$c)$#", $str, $matches)
      AND strlen($matches[1]) == 3
      AND $matches[1]{2} != 'w'
      AND $matches[1]{2} != 'x'
      AND $matches[1]{2} != 'y';
  }
}

class MTrackSearchStemmer extends
    Zend_Search_Lucene_Analysis_TokenFilter {

  public function normalize(Zend_Search_Lucene_Analysis_Token $tok)
  {
    $text = $tok->getTermText();
    $text = PorterStemmer::Stem($text);
    $ntok = new Zend_Search_Lucene_Analysis_Token($text,
                  $tok->getStartOffset(),
                  $tok->getEndOffset());
    $ntok->setPositionIncrement($tok->getPositionIncrement());
    return $tok;
  }
}

class MTrackSearchDateToken extends Zend_Search_Lucene_Analysis_Token {
}

class MTrackSearchAnalyzer extends Zend_Search_Lucene_Analysis_Analyzer_Common
{
  private $_position;
  private $_bytePosition;
  private $_moreTokens = array();

  function reset()
  {
    $this->_position = 0;
    $this->_bytePosition = 0;
  }

  function nextToken()
  {
    if (count($this->_moreTokens))  {
      $tok = array_shift($this->_moreTokens);
      return $tok;
    }
    if ($this->_input == null) {
      return null;
    }

    do {
      /* first check for date fields */

      $is_date = false;
      // 2008-12-22T05:42:42.285445Z
      if (preg_match('/\d{4}-\d\d-\d\d(?:T\d\d:\d\d:\d\d(?:\.\d+)?Z?)?/u',
          $this->_input, $match, PREG_OFFSET_CAPTURE, $this->_bytePosition)) {
        $is_date = true;
      } else if (!preg_match('/[\p{L}\p{N}_]+/u',
          $this->_input, $match, PREG_OFFSET_CAPTURE, $this->_bytePosition)) {
        return null;
      }
      if (!function_exists('mb_strtolower')) {
        $matchedWord = strtolower($match[0][0]);
      } else {
        $matchedWord = mb_strtolower($match[0][0], 'UTF-8');
      }
      $binStartPos = $match[0][1];
      $startPos = $this->_position +
          iconv_strlen(substr($this->_input, $this->_bytePosition,
            $binStartPos - $this->_bytePosition),
            'UTF-8');
      $endPos = $startPos + iconv_strlen($matchedWord, 'UTF-8');
      $this->_bytePosition = $binStartPos + strlen($matchedWord);
      $this->_position = $endPos;

      if ($is_date) {
//        $this->_moreTokens[] = new MTrackSearchDateToken($matchedWord,
//          $startPos, $endPos);

        /* Seems very difficult to allow range searching on strings
         * of the form "2009-10-10", so we just smush it together */
        $no_sep = str_replace(array('-', ':'), array('', ''), $matchedWord);
        list($no_sep) = explode('.', $no_sep);

        /* full date and time */
//        $this->_moreTokens[] = new MTrackSearchDateToken(
//          $no_sep, $startPos, $endPos);

        /* date only */
        $date = substr($no_sep, 0, 8);
        $this->_moreTokens[] = new MTrackSearchDateToken(
          $date, $startPos, $endPos);
      } else {
        $token = new Zend_Search_Lucene_Analysis_Token(
          $matchedWord, $startPos, $endPos);
        $token = $this->normalize($token);
        if ($token !== null) {
          $this->_moreTokens[] = $token;
        }
      }
      if (!$is_date) {
        /* split by underscores and add those tokens too */
        foreach (explode('_', $matchedWord) as $ele) {
          $token  = new Zend_Search_Lucene_Analysis_Token(
            $ele, $startPos, $endPos);
          $token = $this->normalize($token);
          if ($token !== null) {
            $this->_moreTokens[] = $token;
          }
        }
      }
    } while (count($this->_moreTokens) == 0);
    return array_shift($this->_moreTokens);
  }

  function normalize(Zend_Search_Lucene_Analysis_Token $tok)
  {
    if ($tok instanceof MTrackSearchDateToken) {
      return $tok;
    }
    return parent::normalize($tok);
  }
}

class MTrackSearchQueryParser {
  public $toks;
  public $syntax;
  public $query;

  function __construct($q) {
    $this->toks = $this->tokenize($q);
    $this->alltoks = $this->toks;
//    echo '<pre>', htmlentities(var_export($this->toks, true)), '</pre>';

    $this->query = $this->expression();
  }

  function tokenize($string)
  {
    $toks = array();
    while (strlen($string)) {
      if (preg_match("/^\s+/", $string, $M)) {
        $toks[] = array('white', $M[0]);
        $string = substr($string, strlen($M[0]));
        continue;
      }
      if (preg_match("/^[+!(){}^~*?:\\\[\]-]/", $string)) {
        $toks[] = array($string[0]);
        $string = substr($string, 1);
        continue;
      }
      if (!strncmp($string, "&&", 2)) {
        $toks[] = array("&&");
        $string = substr($string, 2);
        continue;
      }
      if (preg_match("/^and\W/i", $string, $M)) {
        $toks[] = array("&&", $M[0]);
        $string = substr($string, 3);
        continue;
      }
      if (preg_match("/^not\W/i", $string, $M)) {
        $toks[] = array("!", $M[0]);
        $string = substr($string, 3);
        continue;
      }
      if (!strncmp($string, "||", 2)) {
        $toks[] = array("||");
        $string = substr($string, 2);
        continue;
      }
      if (preg_match("/^or\W/i", $string, $M)) {
        $toks[] = array("||", $M[0]);
        $string = substr($string, 2);
        continue;
      }
      if (preg_match('/^"([^"]*)"/', $string, $M)) {
        $toks[] = array('literal', $M[1]);
        $string = substr($string, strlen($M[0]));
        continue;
      }
      if (preg_match("/^[a-zA-Z0-9_][a-zA-Z0-9_.+-]*/", $string, $M)) {
        $toks[] = array('literal', $M[0]);
        $string = substr($string, strlen($M[0]));
        continue;
      }
      $string = trim($string);
      if (strlen($string)) {
        echo "Invalid search string: <b>" . htmlentities($string) . "</b>";
        break;
      }
    }
    return $toks;
  }

  function get()
  {
    if (count($this->toks) == 0) {
      return null;
    }
    $t = array_shift($this->toks);
    $args = func_get_args();
    if (count($args)) {
      $ok = false;
      $expected = array();
      foreach ($args as $expect) {
        if ($t[0] == $expect) {
          $ok = true;
          break;
        }
        $expected[] = $expect;
      }
      if (!$ok) {
        $name = $t[0];
        $value = isset($t[1]) ? $t[1] : $t[0];
        $ntoks = count($this->alltoks);
        $rtoks = count($this->toks);
        $hint = '';
        for ($i = 0; $i < $rtoks; $i++) {
          $hint .= htmlentities($this->alltoks[$i][1], ENT_QUOTES, 'utf-8');
        }
        $hint .= "<b>$value</b>";
        foreach ($this->toks as $tok) {
          $hint .= htmlentities($tok[1]);
        }
        throw new Exception(
          "Unexpected token '$value' of type $name expected " .
          join(', ', $expected) . "<br>$hint");
      }
    }
    return $t;
  }

  function peek()
  {
    if (!count($this->toks)) {
      return null;
    }
    $t = $this->toks[0];
    $args = func_get_args();
    if (count($args)) {
      $ok = false;
      foreach ($args as $expect) {
        if ($t[0] == $expect) {
          $ok = true;
          break;
        }
      }
      if (!$ok) {
        return false;
      }
    }
    return $t;
  }

  function try_rule($name) {
    $save = $this->toks;
    try {
      return $this->$name();
    } catch (Exception $e) {
      $this->toks = $save;
      return false;
    }
  }

  function _make_term($t, $field = null)
  {
    if (function_exists('mb_strtolower')) {
      $t[1] = mb_strtolower($t[1], 'UTF-8');
    } else {
      $t[1] = strtolower($t[1]);
    }
    if ($t[0] == 'literal') {
      $bits = preg_split("/\s+/u", $t[1]);

      /* only treat it as a phrase if it is a phrase */
      if (count($bits) > 1) {
        $q = new Zend_Search_Lucene_Search_Query_Phrase;

        foreach ($bits as $w) {
          $t = new Zend_Search_Lucene_Index_Term($w, $field);
          $q->addTerm($t);
        }
        return $q;
      }
    }

    /* underscores and periods!
     * if we're searching for text delimited by underscores, we
     * rewrite that as a phrase search also */
    $bits = preg_split("/[._]/", $t[1]);
    if (count($bits) > 1) {
      $q = new Zend_Search_Lucene_Search_Query_Phrase;

      foreach ($bits as $w) {
        $t = new Zend_Search_Lucene_Index_Term($w, $field);
        $q->addTerm($t);
      }
      return $q;
    }

    return new Zend_Search_Lucene_Index_Term((string)$t[1], $field);
  }

  function term()
  {
    if ($this->peek('literal')) {
      $t = $this->get();
      if ($this->peek(':')) {
        /* specific field */
        $field = $t[1];
        $this->get();

        /* does it have a range? */
        if ($this->peek('[')) {
          $this->get();

          $this->skipwhite();

          $from = $this->get('literal');
          $from = $this->_make_term($from, $field);

          $this->skipwhite();
          $t = $this->get('literal');
          if (strcasecmp($t[1], 'to')) {
            throw new Exception("Expected 'to'");
          }
          $this->skipwhite();

          $to = $this->get('literal');
          $to = $this->_make_term($to, $field);

          $q = new Zend_Search_Lucene_Search_Query_Range(
                $from, $to, true);
          $this->skipwhite();

          $this->get(']');

          return $q;
        }

        $t = $this->get('literal');

        return $this->_make_term($t, $field);
      }
    } else {
      $t = $this->get('literal');
    }

    if ($t) {
      return $this->_make_term($t);
    }
    return null;
  }

  function skipwhite()
  {
    while ($this->peek('white')) {
      $this->get();
    }
  }

  function expression()
  {
    $terms = array();

    while (count($this->toks)) {
      $modifier = null;

      $this->skipwhite();

      if ($this->peek('+')) {
        $this->get();
        $modifier = true;
      }
      if ($this->peek('-')) {
        $this->get();
        $modifier = false;
      }
      if ($modifier === null) {
        $modifier = true;
      }

      $t = $this->term();
      if ($t) {
        $terms[] = array($t, $modifier);
      } else {
        break;
      }
    }

    if (count($terms) == 0) {
      return null;
    }

    if (count($terms) == 1) {
      if ($terms[0][0] instanceof Zend_Search_Lucene_Search_Query) {
        if ($terms[0][1] === null) {
          return $terms[0][0];
        }
      }
    }

    $q = new Zend_Search_Lucene_Search_Query_Boolean();
    foreach ($terms as $term) {
      list($t, $mod) = $term;

      if ($t instanceof Zend_Search_Lucene_Search_Query) {
        $q->addSubquery($t, $mod);
      } else {
        $sq = new Zend_Search_Lucene_Search_Query_MultiTerm;
        $sq->addTerm($t);
        $q->addSubquery($sq, $mod);
      }
    }

    return $q;
  }
}

/* the highlighter insists on using html document things,
 * so we force in our own dummy so that we can present the
 * same text we used initially */
class MTrackSearchLuceneDummyDocument {
  public $text;
  function __construct($text) {
    $this->text = $text;
  }
  function getFieldUtf8Value($name) {
    return $this->text;
  }
}

class MTrackHLText
    implements Zend_Search_Lucene_Search_Highlighter_Interface {
  public $doc;
  public $context = array();
  public $text;
  public $matched = array();

  function setDocument(Zend_Search_Lucene_Document_Html $doc)
  {
    /* sure, I'll get right on that... */
  }

  function getDocument() {
    /* we just return our dummy doc instead */
    return $this->doc;
  }

  function highlight($words) {
    if (!is_array($words)) {
      $words = array($words);
    }
    foreach ($words as $word) {
      foreach ($this->text as $line) {
        $x = strpos($line, $word);
        if ($x !== false) {
          if (isset($this->matched[$word])) {
            $this->matched[$word]++;
          } else {
            $this->matched[$word] = 1;
          }
          if (isset($this->context[$line])) {
            $this->context[$line]++;
          } else {
            $this->context[$line] = 1;
          }
        }
      }
    }
  }

  function __construct($text, $query)
  {
    $this->doc = new MTrackSearchLuceneDummyDocument($text);
    $text = wordwrap($text);
    $this->text = preg_split("/\r?\n/", $text);
    $query->htmlFragmenthighlightMatches($text, 'utf-8', $this);
  }
}

class MTrackSearchResultLucene extends MTrackSearchResult {
  var $_query;

  function getExcerpt($text) {
    $hl = new MTrackHLText($text, $this->_query);
    $lines = array();
    foreach ($hl->context as $line => $count) {
      $line = trim($line);
      if (!strlen($line)) continue;
      $line = htmlentities($line, ENT_QUOTES, 'utf-8');
      foreach ($hl->matched as $word => $wcount) {
        $line = str_replace($word, "<span class='hl'>$word</span>", $line);
      }
      $lines[] = $line;
      if (count($lines) > 6) {
        break;
      }
    }
    $ex = join(" &hellip; ", $lines);
    if (strlen($ex)) {
      return "<div class='excerpt'>$ex</div>";
    }
    return '';
  }
}

class MTrackSearchEngineLucene implements IMTrackSearchEngine
{
  var $idx = null;

  function getIdx() {
    if ($this->idx) return $this->idx;
    $ana = new MTrackSearchAnalyzer;
    $ana->addFilter(new MTrackSearchStemmer);
    Zend_Search_Lucene_Analysis_Analyzer::setDefault($ana);

    $p = MTrackConfig::get('core', 'searchdb');
    if (!is_dir($p)) {
      $idx = Zend_Search_Lucene::create($p);
      chmod($p, 0777);
    } else {
      $idx = Zend_Search_Lucene::open($p);
    }
    $this->index = $idx;
    return $idx;
  }

  public function setBatchMode()
  {
    $idx = $this->getIdx();
    $idx->setMaxBufferedDocs(64);
    $idx->setMergeFactor(15);
  }

  public function commit($optimize = false)
  {
    $idx = $this->getIdx();
    if ($optimize) {
      $idx->optimize();
    }
    $idx->commit();
    $this->idx = null;
  }

  public function add($object, $fields, $replace = false)
  {
    $idx = $this->getIdx();

    if ($replace) {
      $term = new Zend_Search_Lucene_Index_Term($object, 'object');
      foreach ($idx->termDocs($term) as $id) {
        $idx->delete($id);
      }
    }

    $doc = new Zend_Search_Lucene_Document();

    $doc->addField(Zend_Search_Lucene_Field::Text('object', $object, 'utf-8'));
    foreach ($fields as $key => $value) {
      if (!strlen($value)) continue;
      if (!strncmp($key, 'stored:', 7)) {
        $key = substr($key, 7);
        $F = Zend_Search_Lucene_Field::Text($key, $value, 'utf-8');
      } else {
        $F = Zend_Search_Lucene_Field::UnStored($key, $value, 'utf-8');
      }
      $doc->addField($F);
    }

    $idx->addDocument($doc);
  }

  public function search($query) {
    Zend_Search_Lucene::setTermsPerQueryLimit(150);
    Zend_Search_Lucene::setResultSetLimit(250);

    $p = new MTrackSearchQueryParser($query);
    $q = $p->query;
    $idx = $this->getIdx();
    $hits = $idx->find($q);
    $result = array();
    foreach ($hits as $hit) {
      $r = new MTrackSearchResultLucene;
      $r->_query = $q;
      $r->objectid = $hit->object;
      $r->score = $hit->score;
      $result[] = $r;
    }
    return $result;
  }


}


