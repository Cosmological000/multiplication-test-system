<?php
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

$name = trim($_POST['name'] ?? '');
$section = trim($_POST['section'] ?? '');
$grade_level = trim($_POST['grade_level'] ?? '');
$difficulty = $_POST['difficulty'] ?? 'Easy';
$questions = (int)($_POST['questions'] ?? 10);
$operation = $_POST['operation'] ?? 'Multiplication';

if ($questions < 10) $questions = 10;
if ($questions > 50) $questions = 50;

$_SESSION['name'] = htmlspecialchars($name);
$_SESSION['grade_level'] = htmlspecialchars($grade_level);
$_SESSION['section'] =  htmlspecialchars($section);
$_SESSION['operation'] = $operation;
$_SESSION['difficulty'] = $difficulty;
$_SESSION['questions'] = $questions;

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Multiplication Test</title>

<style>
body{
    margin:0;
    font-family:Arial, sans-serif;
    background-image: linear-gradient(black, grey);
    color:#fff;
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
}

.container{
    width:95%;
    max-width:800px;
    background:#1d1d1d;
    padding:20px;
    border-radius:15px;
    text-align:center;
    box-sizing:border-box;
}

#countdown{
    font-size:80px;
    font-weight:bold;
}

#question{
    font-size:clamp(30px, 8vw, 50px);
    margin:30px 0;
    word-wrap:break-word;
}

#answer{
    width:100%;
    max-width:300px;
    padding:12px;
    font-size:24px;
    text-align:center;
    box-sizing:border-box;
}

.top-bar{
    display:flex;
    justify-content:space-between;
    margin-bottom:20px;
    flex-wrap:wrap;
}

.timer{
    font-size:22px;
}

.question-counter{
    font-size:22px;
}

button{
    padding:10px 20px;
    border:none;
    border-radius:8px;
    cursor:pointer;
}

.pause-btn{
    background:#f39c12;
    color:white;
}

.resume-btn{
    background:#27ae60;
    color:white;
}

.blur{
    filter:blur(20px);
    pointer-events:none;
}

.hidden{
    display:none;
}

#pauseOverlay{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.6);
    display:none;
    justify-content:center;
    align-items:center;
    font-size:40px;
    z-index:10;
    pointer-events:none;
}

@media (max-width:768px){

    .top-bar{
        flex-direction:column;
        gap:10px;
        text-align:center;
    }

}

</style>
</head>
<body>

<div id="pauseOverlay">
    TEST PAUSED
</div>

<div class="container">

    <div id="countdown">3</div>

    <div id="testArea" class="hidden">

        <div class="top-bar">

    <div class="timer">
        ⏱ Total Time:
        <span id="stopwatch">0.00</span>s
    </div>

    <div class="timer">
        ⏳ Remaining:
        <span id="questionTimer">30</span>s
    </div>

    <div class="question-counter">
        Question
        <span id="currentQuestion">1</span> /
        <?php echo $questions; ?>
    </div>

</div>

        <div id="questionArea">

            <div id="question"></div>

            <input
                type="number"
                id="answer"
				inputmode="numeric"
                autocomplete="off"
                autofocus
            >

        </div>

        <br>

        <button
            class="pause-btn"
            id="pauseBtn"
        >
            Pause
        </button>

        <button
            class="resume-btn hidden"
            id="resumeBtn"
        >
            Resume
        </button>

    </div>

</div>

<form id="resultForm" action="save_result.php" method="POST">

<input type="hidden" name="correct" id="correctInput">
<input type="hidden" name="wrong" id="wrongInput">
<input type="hidden" name="accuracy" id="accuracyInput">
<input type="hidden" name="total_time" id="totalTimeInput">
<input type="hidden" name="average_time" id="averageTimeInput">
<input type="hidden" name="fastest_time" id="fastestTimeInput">
<input type="hidden" name="slowest_time" id="slowestTimeInput">

</form>

<script>

const TOTAL_QUESTIONS = <?php echo $questions; ?>;
const DIFFICULTY = "<?php echo $difficulty; ?>";
const OPERATION =
"<?php echo $_SESSION['operation']; ?>";

let currentQuestion = 0;
let correct = 0;
let wrong = 0;

let currentAnswer = 0;

let paused = false;

let totalStartTime;
let questionStartTime;

let responseTimes = [];

let stopwatchInterval;
let elapsed = 0;

let questionTimer;
let timeLeft;

let countdown = 3;

const countdownElement = document.getElementById("countdown");
const testArea = document.getElementById("testArea");

const answerInput = document.getElementById("answer");
const questionElement = document.getElementById("question");

function startCountdown(){

    let interval = setInterval(()=>{

        countdown--;

        if(countdown > 0){
            countdownElement.textContent = countdown;
        }
        else if(countdown === 0){
            countdownElement.textContent = "GO!";
        }
        else{

            clearInterval(interval);

            countdownElement.style.display = "none";

            testArea.classList.remove("hidden");

            startTest();
        }

    },1000);
}

startCountdown();

function startStopwatch(){

    totalStartTime = Date.now();

    stopwatchInterval = setInterval(()=>{

        if(!paused){

            elapsed =
            (Date.now() - totalStartTime)/1000;

            document.getElementById("stopwatch")
            .textContent =
            elapsed.toFixed(2);

        }

    },10);
}

function getRange(){

    if(DIFFICULTY === "Easy"){
        return 10;
    }

    if(DIFFICULTY === "Medium"){
        return 20;
    }

    return 30;
}

function getQuestionTime(){

    if(DIFFICULTY === "Easy"){
        return 30;
    }

    if(DIFFICULTY === "Medium"){
        return 20;
    }

    return 10;
}

function startQuestionTimer(){

    clearInterval(questionTimer);

    timeLeft = getQuestionTime();

    document.getElementById("questionTimer").textContent =
    timeLeft;

    questionTimer = setInterval(() => {

        if(paused) return;

        timeLeft--;

        document.getElementById("questionTimer")
        .textContent = timeLeft;

        if(timeLeft <= 5){

            document.getElementById("questionTimer")
            .style.color = "#ff4444";

        }else{

            document.getElementById("questionTimer")
            .style.color = "#ffffff";

        }

        if(timeLeft <= 0){

            clearInterval(questionTimer);

            wrong++;

            responseTimes.push(
                getQuestionTime()
            );

            if(currentQuestion >= TOTAL_QUESTIONS){

                finishTest();

            }else{

                generateQuestion();

            }

        }

    },1000);

}

function generateQuestion(){

    let symbol = "×";

    const range = getRange();

    const a =
    Math.floor(Math.random()*range)+1;

    const b =
    Math.floor(Math.random()*range)+1;

    currentAnswer = a*b;

    switch(OPERATION){

    case "Addition":
        currentAnswer = a + b;
        symbol = "+";
        break;

    case "Subtraction":
        currentAnswer = a - b;
        symbol = "-";
        break;

    case "Division":

        // Create clean division problems
        const divisor = b;
        const quotient = a;

        const dividend = divisor * quotient;

        currentAnswer = quotient;

        questionElement.textContent =
        `${dividend} ÷ ${divisor}`;

        answerInput.value = "";
        answerInput.focus();

        questionStartTime = Date.now();

        currentQuestion++;

        document.getElementById(
            "currentQuestion"
        ).textContent = currentQuestion;

    startQuestionTimer();
	
        return;

    default:
        currentAnswer = a * b;
        symbol = "×";
}

    questionElement.textContent =
`${a} ${symbol} ${b}`;

    answerInput.value = "";

    answerInput.focus();

    questionStartTime = Date.now();

    currentQuestion++;

    document.getElementById(
        "currentQuestion"
    ).textContent = currentQuestion;
	
	startQuestionTimer();
}

function startTest(){

    startStopwatch();

    generateQuestion();
}

answerInput.addEventListener("keydown",function(e){

    if(e.key !== "Enter") return;

    e.preventDefault();

    if(paused) return;

    const answer =
    parseInt(answerInput.value);
	
	clearInterval(questionTimer);

    const response =
    (Date.now()-questionStartTime)/1000;

    responseTimes.push(response);

    if(answer === currentAnswer){
        correct++;
    }else{
        wrong++;
    }

    if(currentQuestion >= TOTAL_QUESTIONS){

        finishTest();

    }else{

        generateQuestion();

    }

});

function finishTest(){

    clearInterval(stopwatchInterval);
    clearInterval(questionTimer);

    const totalTime =
    elapsed;

    const accuracy =
    ((correct/TOTAL_QUESTIONS)*100);

    const average =
    responseTimes.reduce((a,b)=>a+b,0)
    / responseTimes.length;

    const fastest =
    Math.min(...responseTimes);

    const slowest =
    Math.max(...responseTimes);

    document.getElementById("correctInput")
    .value = correct;

    document.getElementById("wrongInput")
    .value = wrong;

    document.getElementById("accuracyInput")
    .value = accuracy.toFixed(2);

    document.getElementById("totalTimeInput")
    .value = totalTime.toFixed(2);

    document.getElementById("averageTimeInput")
    .value = average.toFixed(2);

    document.getElementById("fastestTimeInput")
    .value = fastest.toFixed(2);

    document.getElementById("slowestTimeInput")
    .value = slowest.toFixed(2);

    document.getElementById("resultForm")
    .submit();
}

const pauseBtn =
document.getElementById("pauseBtn");

const resumeBtn =
document.getElementById("resumeBtn");

pauseBtn.addEventListener("click",()=>{

    paused = true;

    document.getElementById(
        "questionArea"
    ).classList.add("blur");

    document.getElementById(
        "pauseOverlay"
    ).style.display = "flex";

    answerInput.disabled = true;

    pauseBtn.classList.add("hidden");

    resumeBtn.classList.remove("hidden");
});

resumeBtn.addEventListener("click",()=>{

    paused = false;

    document.getElementById(
        "questionArea"
    ).classList.remove("blur");

    document.getElementById(
        "pauseOverlay"
    ).style.display = "none";

    answerInput.disabled = false;

    answerInput.focus();

    pauseBtn.classList.remove("hidden");

    resumeBtn.classList.add("hidden");
});

window.addEventListener("beforeunload",function(e){

    e.preventDefault();

    e.returnValue =
    "Leaving will cancel your test.";

});

</script>
</body>
</html>