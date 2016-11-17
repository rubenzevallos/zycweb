<?php # vim:ts=2:sw=2:et:
/* For licensing and copyright terms, see the file named LICENSE */

class MTrackSearchEngineSolr implements IMTrackSearchEngine {
  var $url;

  public function __construct() {
    $this->url = MTrackConfig::get('solr', 'url');
  }

  public function setBatchMode() {
  }

  function post($xml) {
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . $xml;

    $params = array(
      'http' => array(
        'method' => 'POST',
        'content' => $xml,
        'header' => 'Content-Type: text/xml',
      ),
    );
    $ctx = stream_context_create($params);
    for ($i = 0; $i < 10; $i++) {
      $fp = fopen("$this->url/update", 'rb', false, $ctx);
      if ($fp) {
        fclose($fp);
        return;
      }
      sleep(1);
    }
    throw new Exception("unable to update index; is Solr running?\n$xml\n");
  }

  public function commit($optimize = false) {
    $this->post('<optimize/>');
  }

  public function add($object, $fields, $replace = false) {
    $xml = "<add overwrite='true'><doc><field name='id'>$object</field>";
    foreach ($fields as $key => $value) {
      if (!strlen($value)) continue;
      if (!strncmp($key, 'stored:', 7)) {
        $key = substr($key, 7);
      }

      switch ($key) {
        case 'date':
        case 'created':
          $t = strtotime($value);
          $value = date('Y-m-d\\TH:i:s', $t) . 'Z';
          break;
      }
      // avoid: HTTP/1.1 400 Illegal_character_CTRLCHAR_code_12
      // Solr doesn't seem to like these ASCII control characters
      // so we replace them with spaces
      $value = preg_replace("/[\\x00-\\x1f]+/", " ", $value);

      $xml .= "<field name='$key'>" .
        htmlspecialchars($value, ENT_QUOTES, 'utf-8') .
        "</field>";
    }
    $xml .= "</doc></add>";

    $this->post($xml);
  }

  /** returns an array of MTrackSearchResult objects corresponding
   * to matches to the supplied query string */
  public function search($query) {
    $q = http_build_query(array(
      'q' => $query,
      'version' => '2.2',
      'hl' => 'on',
      'hl.fl' => '',
      'hl.usePhraseHighlighter' => 'on',
      'hl.simple.pre' => "<span class='hl'>",
      'hl.simple.post' => "</span>",
      'fl' => 'id,score',
      'wt' => 'json',
      'rows' => 250,
    ));
    $json = file_get_contents("$this->url/select?$q");
    $doc = json_decode($json);
    //echo htmlentities($json);
    //var_dump($doc);
    $result = array();

    /* look for excerpt text */
    $hl = array();
    foreach ($doc->highlighting as $name => $arr) {
      $hl[$name] = array();
      foreach ($arr as $fname => $v) {
        foreach ($v as $a) {
          $hl[$name][] = $a;
        }
      }
    }

    foreach ($doc->response->docs as $doc) {
      $r = new MTrackSearchResult;
      $r->objectid = $doc->id;
      $r->score = $doc->score;
      $r->excerpt = null;
      if (isset($hl[$r->objectid])) {
        $r->excerpt = "<div class='excerpt'>" .
          join("\n", $hl[$r->objectid]) .
          "</div>";
      }
      $result[] = $r;
    }

    return $result;
  }
}
