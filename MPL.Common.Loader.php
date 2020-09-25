<?php
$levels = count(explode(DIRECTORY_SEPARATOR, getcwd()));
$path = 'MPL.Common';
$target = null;
while ($levels-- > 0) {
  if (is_dir($path) && file_exists($path . DIRECTORY_SEPARATOR . '_requires.php')) {
    $target = $path . DIRECTORY_SEPARATOR . '_requires.php';
    break;
  }
  $path = '..' . DIRECTORY_SEPARATOR . $path;
}
if ($target) {
  require_once $target;
} else {
  die('Unable to load MPL Common library');
}
?>