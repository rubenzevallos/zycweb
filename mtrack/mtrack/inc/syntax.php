<?php # vim:ts=2:sw=2:et:
/* For licensing and copyright terms, see the file named LICENSE */

class MTrackSyntaxHighlight {
  static $schemes = array(
    '' => 'No syntax highlighting',
    'wezterm' => "Wez's Terminal",
    'zenburn' => "Zenburn",
    'vibrant-ink' => "Vibrant Ink",
  );
  static $lang_by_ext = array(
      'c' => 'cpp',
      'cc' => 'cpp',
      'cpp' => 'cpp',
      'h' => 'cpp',
      'hpp' => 'cpp',
      'icl' => 'cpp',
      'ipp' => 'cpp',
      'css' => 'css',
      'php' => 'php',
      'php3' => 'php',
      'php4' => 'php',
      'php5' => 'php',
      'phtml' => 'php',
      'pl' => 'perl',
      'pm' => 'perl',
      't' => 'perl',
      'bash' => 'shell',
      'sh' => 'shell',
      'js' => 'javascript',
      'json' => 'javascript',
      'vb' => 'vb',
      'xml' => 'xml',
      'xsl' => 'xml',
      'xslt' => 'xml',
      'xsd' => 'xml',
      'html' => 'xml',
      'diff' => 'diff',
      'patch' => 'diff',
      'wiki' => 'wiki',
    );
  static $langs = array(
    '' => 'No particular file type',
    'cpp' => 'C/C++',
    'css' => 'CSS (Cascading Style Sheet)',
    'php' => 'PHP',
    'perl' => 'Perl',
    'shell' => 'Shell script',
    'javascript' => 'Javascript',
    'vb' => 'Visual Basic',
    'xml' => 'HTML, XML, XSL',
    'wiki' => 'Wiki Markup',
    'diff' => 'Diff/Patch',
  );

  static function inferFileTypeFromContents($data) {
    if (preg_match("/vim:.*ft=(\S+)/", $data, $M)) {
      return $M[1];
    }
    if (preg_match("/^#!.*env\s+(\S+)/", $data, $M)) {
      return $M[1];
    }
    if (preg_match("/^#!\s*(\S+)/", $data, $M)) {
      return basename($M[1]);
    }
    return null;
  }

  static function highlightSource($data, $type = null, $filename = null, $line_numbers = false) {
    if ($type === null) {
      $type = self::inferFileTypeFromContents($data);
      if ($type === null && $filename !== null) {
        if (preg_match("/\.([^.]+)$/", $filename, $M)) {
          $ext = strtolower($M[1]);
          if (isset(self::$lang_by_ext[$ext])) {
            $type = self::$lang_by_ext[$ext];
          }
        }
      }
    }
    if ($type == 'diff') {
      return mtrack_diff($data);
    }
    if (strlen($type) && isset(self::$langs[$type])) {
      require_once dirname(__FILE__) . '/hyperlight/hyperlight.php';
      $hl = new Hyperlight($type);
      $hdata = $hl->render($data);
    } else {
      $hdata = htmlentities($data);
    }
    if (!$line_numbers) {
      return "<span class='source-code wezterm'>$hdata</span>";
    }
    $lines = preg_split("/\r?\n/", $data);
    $html = <<<HTML
<table class='codeann'>
  <tr>
    <th class='line'>line</th>
    <th class='code'>code</th>
  </tr>
HTML;
    $nlines = count($lines);
    for ($i = 1; $i <= $nlines; $i++) {
      $html .= "<tr><td class='line'><a name='l$i'></a><a href='#l$i'>$i</a></td>";
      if ($i == 1) {
        $html .= "<td rowspan='$nlines' width='100%' class='source-code wezterm'>$hdata</td>";
      }
      $html .= "</tr>\n";
    }
    return $html . "</table>\n";
  }

  static function getSchemeSelect($selected = 'wezterm') {
    $html = <<<HTML
<select class='select-hl-scheme'>
HTML;
    foreach (self::$schemes as $k => $v) {
      $sel = $selected == $k ? " selected" : '';
      $html .= "<option value='$k'$sel>" .
        htmlentities($v, ENT_QUOTES, 'utf-8') .
        "</option>\n";
    }
    return $html . "</select>";
  }

  static function getLangSelect($name, $value) {
    return mtrack_select_box($name, self::$langs, $value);
  }

}

