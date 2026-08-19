<?php
session_start();

$_SESSION['role'] = 'student';

header("Location: index.php");
exit();
?>