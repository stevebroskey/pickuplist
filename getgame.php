<?php

$id = $_GET['id'];
if (!isset($id)) exit;

header("Content-type: text/plain");

$sql = urlencode("SELECT latitude, longitude, data FROM 1077209 WHERE id = '$id'");
$url = "http://www.google.com/fusiontables/api/query?sql=$sql";

$fh = fopen($url, 'r');
fgets($fh);

$data = "";
while (!feof($fh)) {
  $data .= fread($fh, 1024);
}
fclose($fh);

$data = str_replace('""', '"', trim($data));

if (preg_match('/^([\\-0-9\.]+),([\\-0-9\.]+),\"(.+)\"$/s', $data, $match)) {
  print("{ \"latitude\": \"$match[1]\", \"longitude\": \"$match[2]\", \"data\": $match[3] }");
}

?>