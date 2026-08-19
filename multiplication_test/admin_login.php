<?php
session_start();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $password = $_POST['password'] ?? '';

    if ($password === "ICT2026") {

        $_SESSION['role'] = 'admin';

        header("Location: leaderboard.php");
        exit();

    } else {

        $error = "Invalid password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Login</title>

<style>

body{
    background:#121212;
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
}

input{
    width:100%;
    padding:12px;
    margin:10px 0;
    box-sizing:border-box;
}

button{
    width:100%;
    padding:12px;
    background:#1565c0;
    color:white;
    border:none;
    border-radius:5px;
}

.error{
    color:#ff6b6b;
}
</style>

</head>
<body>

<div class="container">

<h2>Admin Login</h2>

<?php if($error): ?>
<p class="error"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST">

<input
type="password"
name="password"
placeholder="Admin Password"
required
>

<button type="submit">
Login
</button>

</form>

</div>

</body>
</html>