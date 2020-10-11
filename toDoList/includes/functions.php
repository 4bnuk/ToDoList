<?php
function u($string) {
  return urlencode($string);
}

function h($string) {
  return htmlspecialchars($string);
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = h($data);
  return $data;
}

?>