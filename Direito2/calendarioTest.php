<?PHP

$year = date('Y');
$month = date('n');
$day = date('j');

$year = date('Y');
$month = 5;
$day = date('j');

$daysInMonth = date("t",mktime(0,0,0,$month,1,$year));

$firstDay = date("w", mktime(0,0,0,$month,1,$year)) - 1;

echo "<br />daysInMonth=", $daysInMonth;
echo "<br />firstDay=", $firstDay;

$dayContent[1]['href'] = '/2011/abr/1';
$dayContent[1]['title'] = '1 de abril de 2011';

$dayContent[5]['href'] = '/2011/abr/5';
$dayContent[5]['title'] = '5 de abril de 2011';

$dayContent[20]['href'] = '/2011/abr/20';
$dayContent[20]['title'] = '20 de abril de 2011';

$dayMonth = array();

$days = ceil(($firstDay + $daysInMonth) / 7) * 7;

echo "<br />days=", $days;

$ul['class'] = 'calendar mes janeiro';

$monthContent['caption'] = 'Janeiro';
$monthContent['href'] = '/2011/jan';
$monthContent['class'] = 'title';

$caption = $monthContent['caption'];

$weekContent[0]['caption'] = 'Dom';
$weekContent[0]['class'] = 'day';

$weekContent[1]['caption'] = 'Seg';
$weekContent[1]['class'] = 'day';

$weekContent[2]['caption'] = 'Ter';
$weekContent[2]['class'] = 'day';

$weekContent[3]['caption'] = 'Qua';
$weekContent[3]['class'] = 'day';

$weekContent[4]['caption'] = 'Qui';
$weekContent[4]['class'] = 'day';

$weekContent[5]['caption'] = 'Sex';
$weekContent[5]['class'] = 'day';

$weekContent[6]['caption'] = 'Sab';
$weekContent[6]['class'] = 'day';

if ($class = $monthContent['class']) $class = " class=\"$class\"";
if ($target = $monthContent['target']) $target = " target=\"$target\"";

if ($href = $monthContent['href']) $caption = "<a href=\"$href\"$class$target>$caption</a>";

if ($class = $ul['class']) $class = " class=\"$class\"";

$result = "<ul$class>";

$result .= "<li$class>$caption</li>";

for($i = 0; $i < 7; $i++) {
  $caption = $weekContent[$i]['caption'];

  if ($class = $weekContent[$i]['class']) $class = " class=\"$class\"";
  if ($target = $weekContent[$i]['target']) $target = " target=\"$target\"";

  if ($href = $weekContent[$i]['href']) $caption = "<a href=\"$href\"$class$target>$caption</a>";

  $result .= "<li$class>$caption</li>";
}

for($i = 0; $i < $days; $i++) {
  if (($caption = ($i - $firstDay)) > 0 && $caption <= $daysInMonth) {
    if (is_array($dayContent[$caption])) $dayMonth[$i] = $dayContent[$caption];

    $dayMonth[$i]['caption'] = $caption;

  } else {
    $dayMonth[$i]['class'] = 'empty';
  }

  $caption = $dayMonth[$i]['caption'];

  if ($class = $dayMonth[$i]['class']) $class = " class=\"$class\"";
  if ($target = $dayMonth[$i]['target']) $target = " target=\"$target\"";

  if ($href = $dayMonth[$i]['href']) $caption = "<a href=\"$href\"$class$target>$caption</a>";

  $result .= "<li$class>$caption</li>";
}

$result .= "</ul>";

echo $result;

var_dump($dayMonth);
