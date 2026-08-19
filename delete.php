<?php
session_start();

require_once "config/database.php";

if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'admin'
) {
    die("Access Denied");
}

$id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare(
    "DELETE FROM leaderboard WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: leaderboard.php");
exit();
?>