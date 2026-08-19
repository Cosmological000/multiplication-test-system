<?php
session_start();

if (isset($_SESSION['role'])) {

    if ($_SESSION['role'] === 'student') {
        header("Location: index.php");
        exit();
    }

    if ($_SESSION['role'] === 'admin') {
        header("Location: leaderboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>System Login</title>

<style>
.logo-container{
    position:fixed;
    top:20px;
    right:20px;
    z-index:999;
}

.logo-container img{
    width:100px;
    height:auto;
}
body{
    background-image: linear-gradient(white, black);
    color:white;
    font-family:Arial;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.container{
    background:#1e1e1e;
    padding:40px;
    border-radius:15px;
    width:400px;
    text-align:center;
}

.btn{
    display:block;
    margin:15px 0;
    padding:15px;
    border:none;
    border-radius:10px;
    text-decoration:none;
    color:white;
    font-size:18px;
}

.student{
    background:#2e7d32;
}

.admin{
    background:#1565c0;
}
</style>

</head>
<body>

<div class="logo-container">
    <img src="assets/images/logo.png" alt="System Logo">
</div>

<div class="container">

<h2>What is your position in school?</h2>

<a href="student_login.php" class="btn student">
Student
</a>

<a href="admin_login.php" class="btn admin">
Admin
</a>

</div>

</body>
</html>