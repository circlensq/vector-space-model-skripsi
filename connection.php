<?php
    // $user = "root";
    // $pass = "";
    // $db = "buku_umn";
    // $host = "localhost";
    // $conn = mysql_connect($host, $user, $pass);
    // mysql_select_db($db, $conn);

$servername = "localhost";
$username = "root";
$password = "";
$database = "skripsi";
// Create connection
$conn = new mysqli($servername,$username,$password,$database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
echo "Connected successfully";

