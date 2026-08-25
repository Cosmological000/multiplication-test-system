<?php
$host = "sakura.proxy.rlwy.net";
$user = "root";
$pass = "your_actual_mysql_password";
$db   = "railway";
$port = 38834;

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>