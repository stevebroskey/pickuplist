<?php

function ClientLogin($username, $password, $service) {
  $fields = array('accountType' => 'GOOGLE',
		  'Email'       => $username,
		  'Passwd'      => $password,
		  'source'      => 'IanCooper-Locate_IO-0.1',
		  'service'     => $service);
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/accounts/ClientLogin');
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
  
  $data = curl_exec($ch);
  if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) return false;
  
  $lines = explode("\n", $data);
  array_pop($lines);

  $tokens = array();
  foreach ($lines as $line) {
    $parts = explode('=', $line, 2);
    $tokens[$parts[0]] = $parts[1];
  }
  return $tokens;
}

class FusionTable {

  private $auth;

  function FusionTable($auth) {
    $this->auth = $auth;
  }

  function Query($query) {
    $headers = array('Authorization: GoogleLogin auth='.$this->auth);
    $results = array();

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if (preg_match('/^show|^describe|^select/i', $query)) {
      $url = 'https://www.google.com/fusiontables/api/query?sql='.urlencode($query);

      curl_setopt($ch, CURLOPT_URL, $url);
    } else {
      $params = array('sql' => $query);
      $url = 'https://www.google.com/fusiontables/api/query';

      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    }
  
    $lines = explode("\n", curl_exec($ch));
    if (curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) return false;
    array_pop($lines);
    
    $cols = str_getcsv($lines[0]);
    $col_count = count($cols);
    $line_count = count($lines);
    for ($i = 1; $i < $line_count; $i++) {
      $cells = str_getcsv($lines[$i]);
      $result_row = array();
      for ($j = 0; $j < $col_count; $j++) {
	$result_row[$cols[$j]] = $cells[$j];
      }
      $results[$i - 1] = $result_row;
    }
    
    return $results;
  }
}

?>