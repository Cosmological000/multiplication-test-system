<?php
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

require_once "config/database.php";

if (!isset($_SESSION['result_id'])) {
    header("Location: index.php");
    exit();
}

$result_id = (int)$_SESSION['result_id'];

$sql = "SELECT * FROM leaderboard WHERE id = ? LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $result_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$row = $result->fetch_assoc();

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Test Results</title>

<style>

body{
    margin:0;
    background:#121212;
    color:#ffffff;
    font-family:Arial, Helvetica, sans-serif;
}

.container{
    width:90%;
    max-width:900px;
    margin:40px auto;
    background:#1e1e1e;
    padding:30px;
    border-radius:15px;
}

h1{
    text-align:center;
    margin-bottom:30px;
}

.rating{
    text-align:center;
    font-size:32px;
    font-weight:bold;
    margin-bottom:30px;
}

.grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:15px;
}

.card{
    background:#292929;
    padding:18px;
    border-radius:10px;
}

.label{
    color:#bbbbbb;
    font-size:14px;
    margin-bottom:5px;
}

.value{
    font-size:24px;
    font-weight:bold;
}

.actions{
    display:flex;
    gap:15px;
    margin-top:30px;
}

.btn{
    flex:1;
    padding:15px;
    border:none;
    border-radius:10px;
    cursor:pointer;
    text-decoration:none;
    text-align:center;
    font-size:16px;
    color:white;
}

.home{
    background:#2e7d32;
}

.leaderboard{
    background:#1565c0;
}

@media(max-width:700px){

    .grid{
        grid-template-columns:1fr;
    }

    .actions{
        flex-direction:column;
    }

}

</style>
</head>
<body>

<div class="container">

    <h1>Multiplication Test Results</h1>

    <div class="rating">
        <?php echo htmlspecialchars($row['rating']); ?>
    </div>

    <div class="grid">

        <div class="card">
            <div class="label">Examinee</div>
            <div class="value">
                <?php echo htmlspecialchars($row['name']); ?>
            </div>
        </div>
		
		<div class="card">
    <div class="label">Grade Level</div>
    <div class="value">
        <?php echo htmlspecialchars($row['grade_level']); ?>
    </div>
</div>
		
		<div class="card">
    <div class="label">Section</div>
    <div class="value">
        <?php echo htmlspecialchars($row['section']); ?>
    </div>
</div>
		
		<div class="card">
    <div class="label">Operation</div>
    <div class="value">
        <?php echo htmlspecialchars($row['operation']); ?>
    </div>
</div>

        <div class="card">
            <div class="label">Difficulty</div>
            <div class="value">
                <?php echo htmlspecialchars($row['difficulty']); ?>
            </div>
        </div>

        <div class="card">
            <div class="label">Questions</div>
            <div class="value">
                <?php echo $row['questions']; ?>
            </div>
        </div>

        <div class="card">
            <div class="label">Correct Answers</div>
            <div class="value">
                <?php echo $row['correct_answers']; ?>
            </div>
        </div>

        <div class="card">
            <div class="label">Wrong Answers</div>
            <div class="value">
                <?php echo $row['wrong_answers']; ?>
            </div>
        </div>

        <div class="card">
            <div class="label">Accuracy</div>
            <div class="value">
                <?php echo number_format($row['accuracy'],2); ?>%
            </div>
        </div>

        <div class="card">
            <div class="label">Total Time</div>
            <div class="value">
                <?php echo number_format($row['total_time'],2); ?> s
            </div>
        </div>

        <div class="card">
            <div class="label">Average Time Per Question</div>
            <div class="value">
                <?php echo number_format($row['average_time'],2); ?> s
            </div>
        </div>

        <div class="card">
            <div class="label">Fastest Response</div>
            <div class="value">
                <?php echo number_format($row['fastest_time'],2); ?> s
            </div>
        </div>

        <div class="card">
            <div class="label">Slowest Response</div>
            <div class="value">
                <?php echo number_format($row['slowest_time'],2); ?> s
            </div>
        </div>

        <div class="card">
            <div class="label">Date Taken</div>
            <div class="value" style="font-size:18px;">
                <?php echo htmlspecialchars($row['date_taken']); ?>
            </div>
        </div>

    </div>

    <div class="actions">

        <a href="index.php" class="btn home">
            Take Another Test
        </a>

        <a href="leaderboard.php" class="btn leaderboard">
            View Leaderboard
        </a>

    </div>

</div>

</body>
</html>