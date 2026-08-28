<?php
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

require_once("database.php");

/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

$isAdmin =
isset($_SESSION['role']) &&
$_SESSION['role'] === 'admin';

$search = trim($_GET['search'] ?? '');
$difficulty = trim($_GET['operation'] ?? '');
$grade_level = trim($_GET['grade_level'] ?? '');
$section = trim($_GET['section'] ?? '');
$operation = trim($_GET['operation'] ?? '');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$limit = 20;
$offset = ($page - 1) * $limit;

/*
|--------------------------------------------------------------------------
| Build Query
|--------------------------------------------------------------------------
*/

$where = [];
$params = [];
$types = "";

if (!empty($search)) {
    $where[] = "name LIKE ?";
    $params[] = "%" . $search . "%";
    $types .= "s";
}

if (!empty($operation)) {
    $where[] = "operation = ?";
    $params[] = $operation;
    $types .= "s";
}

if (!empty($grade_level)) {

    $where[] = "grade_level = ?";
    $params[] = $grade_level;
    $types .= "s";

}

if (!empty($section)) {

    $where[] = "section = ?";
    $params[] = $section;
    $types .= "s";

}

$whereSql = "";

if (!empty($where)) {
    $whereSql = "WHERE " . implode(" AND ", $where);
}

/*
|--------------------------------------------------------------------------
| Count Records
|--------------------------------------------------------------------------
*/

$countSql = "
SELECT COUNT(*) AS total
FROM leaderboard
$whereSql
";

$countStmt = $conn->prepare($countSql);

if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}

$countStmt->execute();

$countResult = $countStmt->get_result();
$totalRows = $countResult->fetch_assoc()['total'];

$totalPages = ceil($totalRows / $limit);

$countStmt->close();

/*
|--------------------------------------------------------------------------
| Fetch Records
|--------------------------------------------------------------------------
*/

$sql = "
SELECT *
FROM leaderboard
$whereSql
ORDER BY accuracy DESC, average_time ASC
LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql);

$fetchTypes = $types . "ii";
$fetchParams = $params;

$fetchParams[] = $limit;
$fetchParams[] = $offset;

$stmt->bind_param($fetchTypes, ...$fetchParams);

$stmt->execute();

$results = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Leaderboard</title>
<link rel="stylesheet" href="assets/style.css">

<style>
.table-wrapper{
    overflow-x:auto;
}
</style>

</head>
<body>

<div class="container">

<h1>Leaderboard</h1>

<div class="filters">

<form method="GET">

<div class="filter-row">

<input
type="text"
name="search"
placeholder="Search examinee..."
value="<?php echo htmlspecialchars($search); ?>"
>

<select name="operation">

    <option value="">All Operations</option>

    <option value="Addition"
    <?php if($operation=="Addition") echo "selected"; ?>>
        Addition
    </option>

    <option value="Subtraction"
    <?php if($operation=="Subtraction") echo "selected"; ?>>
        Subtraction
    </option>

    <option value="Multiplication"
    <?php if($operation=="Multiplication") echo "selected"; ?>>
        Multiplication
    </option>

    <option value="Division"
    <?php if($operation=="Division") echo "selected"; ?>>
        Division
    </option>

</select>

<select name="difficulty">

<option value="">All Difficulties</option>

<option value="Easy"
<?php if($difficulty=="Easy") echo "selected"; ?>>
Easy
</option>

<option value="Medium"
<?php if($difficulty=="Medium") echo "selected"; ?>>
Medium
</option>

<option value="Hard"
<?php if($difficulty=="Hard") echo "selected"; ?>>
Hard
</option>

</select>

<select name="grade_level" id="gradeLevelFilter">

    <option value="">All Grades</option>

    <option value="Grade 7">Grade 7</option>
    <option value="Grade 8">Grade 8</option>
    <option value="Grade 9">Grade 9</option>
    <option value="Grade 10">Grade 10</option>
    <option value="Grade 11">Grade 11</option>
    <option value="Grade 12">Grade 12</option>

</select>

<select name="section" id="sectionFilter">

    <option value="">All Sections</option>

</select>

<button type="submit">
Filter
</button>

</div>

</form>

</div>

<div class="table-wrapper">

<table>

<thead>

<tr>

<th>Rank</th>
<th>Name</th>
<th>Grade Level</th>
<th>Section</th>
<th>Operation</th>
<th>Questions</th>
<th>Correct</th>
<th>Wrong</th>
<th>Accuracy</th>
<th>Total Time</th>
<th>Average</th>
<th>Fastest</th>
<th>Slowest</th>
<th>Rating</th>
<th>Date</th>
<th>Progress</th>
<th>Difference</th>

<?php if($isAdmin): ?>
<th>Actions</th>
<?php endif; ?>

</tr>

</thead>

<tbody>

<?php

$rank = $offset + 1;

while($row = $results->fetch_assoc()):

?>

<tr>

<td class="rank">
<?php echo $rank++; ?>
</td>

<td>
<?php echo htmlspecialchars($row['name']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['grade_level']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['section']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['operation']); ?>
</td>

<td>
<?php echo $row['questions']; ?>
</td>

<td>
<?php echo $row['correct_answers']; ?>
</td>

<td>
<?php echo $row['wrong_answers']; ?>
</td>

<td>
<?php echo number_format($row['accuracy'],2); ?>%
</td>

<td>
<?php echo number_format($row['total_time'],2); ?>s
</td>

<td>
<?php echo number_format($row['average_time'],2); ?>s
</td>

<td>
<?php echo number_format($row['fastest_time'],2); ?>s
</td>

<td>
<?php echo number_format($row['slowest_time'],2); ?>s
</td>

<td>
<?php echo htmlspecialchars($row['rating']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['date_taken']); ?>
</td>

<td>
<?php echo htmlspecialchars($row['progress_status']); ?>
</td>

<td>
<?php echo number_format($row['progress_accuracy'],2); ?>%
</td>

<?php if($isAdmin): ?>
<td>
    <a
        href="delete.php?id=<?php echo $row['id']; ?>"
        onclick="return confirm('Delete this record?')">
        Delete
    </a>
</td>
<?php endif; ?>

</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>

<div class="pagination">

<?php for($i=1;$i<=$totalPages;$i++): ?>

<a
class="page-link <?php echo ($i==$page)?'active':''; ?>"
href="?page=<?php echo $i; ?>
&search=<?php echo urlencode($search); ?>
&difficulty=<?php echo urlencode($difficulty); ?>
&operation=<?php echo urlencode($operation); ?>
&section=<?php echo urlencode($section); ?>"
>
<?php echo $i; ?>
</a>

<?php endfor; ?>

</div>

<div class="actions">

<a href="index.php" class="home-btn">
Back to Home
</a>

</div>

</div>
<script>

const selectedSection =
"<?php echo htmlspecialchars($section); ?>";

const gradeSections = {

    "Grade 7": [
        "Arowana",
        "Beta",
        "Carp",
        "Dory",
        "Orca",
        "Guppy"
    ],

    "Grade 8": [
        "Flux",
        "Bolt",
        "Wrench",
        "Jigsaw",
        "Rod",
        "Fixtures"
    ],

    "Grade 9": [
        "Circuit",
        "Hertz",
        "Ampere",
        "Frequency"
    ],

    "Grade 10": [
        "Modem",
        "Ram",
        "Java",
        "Foxpro",
        "Ruby",
        "Icon"
    ],

    "Grade 11": [
        "Yandex",
        "Netscape",
        "Google",
        "Firefox",
        "Maxthon",
        "Edge"
    ],

    "Grade 12": [
        "Facebook",
        "Instagram",
        "Viber",
        "Twitter",
        "Telegram",
        "Pinterest"
    ]

};

const gradeFilter =
document.getElementById("gradeLevelFilter");

const sectionFilter =
document.getElementById("sectionFilter");

function loadSections() {

    const grade = gradeFilter.value;

    sectionFilter.innerHTML =
    '<option value="">All Sections</option>';

    if(gradeSections[grade]) {

        gradeSections[grade].forEach(section => {

            const option =
            document.createElement("option");

            option.value = section;
            option.textContent = section;
			
			if(section === selectedSection){
    option.selected = true;
}

            sectionFilter.appendChild(option);

        });

    }

}

gradeFilter.addEventListener(
    "change",
    loadSections
);

// Load sections automatically
// if grade is already selected

loadSections();

</script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
