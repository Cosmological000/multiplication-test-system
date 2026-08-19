<?php
session_start();

require_once "config/database.php";

/*
|--------------------------------------------------------------------------
| Validate Session
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['name']) ||
	!isset($_SESSION['grade_level']) ||
	!isset($_SESSION['section']) ||
	!isset($_SESSION['operation']) ||
    !isset($_SESSION['difficulty']) ||
    !isset($_SESSION['questions'])
) {
    header("Location: index.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| Validate POST Data
|--------------------------------------------------------------------------
*/

$correct      = isset($_POST['correct']) ? (int)$_POST['correct'] : 0;
$wrong        = isset($_POST['wrong']) ? (int)$_POST['wrong'] : 0;
$accuracy     = isset($_POST['accuracy']) ? (float)$_POST['accuracy'] : 0;
$total_time   = isset($_POST['total_time']) ? (float)$_POST['total_time'] : 0;
$average_time = isset($_POST['average_time']) ? (float)$_POST['average_time'] : 0;
$fastest_time = isset($_POST['fastest_time']) ? (float)$_POST['fastest_time'] : 0;
$slowest_time = isset($_POST['slowest_time']) ? (float)$_POST['slowest_time'] : 0;

$name       = $_SESSION['name'];
$grade_level = $_SESSION['grade_level'];
$section = $_SESSION['section'];
$operations  = $_SESSION['operation'];
$difficulty = $_SESSION['difficulty'];
$questions  = $_SESSION['questions'];
$progress_status = "Baseline";
$progress_accuracy = 0;

$previousSql = "
SELECT accuracy
FROM leaderboard
WHERE name = ?
ORDER BY id DESC
LIMIT 1
";

$previousStmt = $conn->prepare($previousSql);
$previousStmt->bind_param("s", $name);
$previousStmt->execute();

$previousResult =
$previousStmt->get_result();

if($previousRow =
$previousResult->fetch_assoc()){

    $previousAccuracy =
    $previousRow['accuracy'];

    $difference =
    $accuracy - $previousAccuracy;

    $progress_accuracy =
    round($difference, 2);

    if($difference > 0){

        $progress_status = "Improved";

    }elseif($difference < 0){

        $progress_status = "Declined";

    }else{

        $progress_status = "No Change";

    }
}

$previousStmt->close();
/*
|--------------------------------------------------------------------------
| Determine Rating
|--------------------------------------------------------------------------
*/

if ($accuracy >= 95) {
    $rating = "Genius Level";
}
elseif ($accuracy >= 85) {
    $rating = "Excellent";
}
elseif ($accuracy >= 70) {
    $rating = "Above Average";
}
elseif ($accuracy >= 50) {
    $rating = "Average";
}
else {
    $rating = "Needs Practice";
}

/*
|--------------------------------------------------------------------------
| Save Result
|--------------------------------------------------------------------------
*/

$sql = "
INSERT INTO leaderboard
(
    name,
	grade_level,
	section,
    difficulty,
    operation,
	questions,
    correct_answers,
    wrong_answers,
    accuracy,
    total_time,
    average_time,
    fastest_time,
    slowest_time,
    rating,
	progress_status,
	progress_accuracy
)
VALUES
(
    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
)
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param(
    "sssssiiidddddsss",
    $name,
	$grade_level,
	$section,
    $difficulty,
	$operations,
    $questions,
    $correct,
    $wrong,
    $accuracy,
    $total_time,
    $average_time,
    $fastest_time,
    $slowest_time,
    $rating,
	$progress_status,
	$progress_accuracy
);

if (!$stmt->execute()) {
    die("Database Error: " . $stmt->error);
}

/*
|--------------------------------------------------------------------------
| Store Result ID
|--------------------------------------------------------------------------
*/

$_SESSION['result_id'] = $conn->insert_id;

/*
|--------------------------------------------------------------------------
| Cleanup
|--------------------------------------------------------------------------
*/

$stmt->close();
$conn->close();

/*
|--------------------------------------------------------------------------
| Redirect to Results Page
|--------------------------------------------------------------------------
*/

header("Location: result.php");
exit();
?>