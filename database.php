<?php
$host = "sakura.proxy.rlwy.net";
$user = "root";
$pass = "ofcvNRzkpHfbwIXFWHdWkhmagZrOCdzx";
$db   = "railway";
$port = 38834;

$conn = new mysqli($host, $user, $pass, $db, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>