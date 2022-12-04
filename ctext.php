<?php
include 'crypto.php';
$crypt = new crypto();
$text=exec('getmac'); 
$mac = strtok($text,' ');
//echo "$mac<br/>";
$text1 = $crypt->cypher($text);
echo $text1;
?>