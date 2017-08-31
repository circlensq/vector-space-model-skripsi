<?php
$user = "root";
$pass = "";
$db = "buku_umn";
$host = "localhost";
$connection = mysql_connect($host, $user, $pass);
mysql_select_db($db, $connection);
?>