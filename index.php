<?php
session_start();

if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operations Intelligence Test</title>

    <link rel="stylesheet" href="assets/style.css">

    <style>
	.logo-container{
    position:fixed;
    top:20px;
    right:20px;
    z-index:999;
}

.logo-container img{
    width:clamp(60px, 15vw, 120px);
}
        body{
            margin:0;
            font-family:Arial, Helvetica, sans-serif;
            background-image: linear-gradient(black, white);
            color:#ffffff;
            display:flex;
            justify-content:center;
            align-items:center;
            min-height:100vh;
        }

        .container{
            width:90%;
            max-width:700px;
            background:#1c1c1c;
            padding:40px;
            border-radius:15px;
            box-shadow:0 0 20px rgba(0,0,0,.5);
        }

        h1{
            text-align:center;
            margin-bottom:10px;
        }

        .subtitle{
            text-align:center;
            color:#aaaaaa;
            margin-bottom:30px;
        }

        .form-group{
            margin-bottom:20px;
        }

        label{
            display:block;
            margin-bottom:8px;
        }

        input,
        select{
            width:100%;
            padding:12px;
            border:none;
            border-radius:8px;
            background:#2a2a2a;
            color:white;
            font-size:16px;
        }

        .checkbox-group{
            display:flex;
            align-items:center;
            gap:10px;
            margin-top:10px;
        }

        .checkbox-group input{
            width:auto;
        }

        .buttons{
            display:flex;
            gap:10px;
            margin-top:25px;
        }
		
		@media (max-width:768px){

    .buttons{
        flex-direction:column;
    }

    .buttons button,
    .buttons a{
        width:100%;
        box-sizing:border-box;
    }

}

        button,
        .leaderboard-btn{
            flex:1;
            padding:14px;
            border:none;
            border-radius:8px;
            cursor:pointer;
            text-decoration:none;
            text-align:center;
            font-size:16px;
        }

        .start-btn{
            background:#2e7d32;
            color:white;
        }

        .start-btn:hover{
            background:#388e3c;
        }

        .leaderboard-btn{
            background:#1565c0;
            color:white;
        }

        .leaderboard-btn:hover{
            background:#1976d2;
        }

        .note{
            margin-top:25px;
            color:#aaaaaa;
            font-size:14px;
            line-height:1.6;
        }

        .difficulty-info{
            margin-top:20px;
            background:#252525;
            padding:15px;
            border-radius:10px;
        }

        .difficulty-info ul{
            padding-left:20px;
        }
		.logout{
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
.logout-btn{
    display:inline-block;
    padding:8px 24px;
    background:#c62828;
    color:white;
    text-decoration:none;
    border-radius:8px;
    font-weight:bold;
    transition:0.3s;
}

.logout-btn:hover{
    background:#e53935;
    transform:translateY(-2px);
}

.logout-container{
    margin-bottom:20px;
}
    </style>
</head>
<body>

<div class="logo-container">
    <img src="assets/images/logo.png" alt="System Logo">
</div>

<div class="container">

    <h1>Operation Intelligence Test</h1>

    <p class="subtitle">
        Measure Operation skill, reaction speed, and mental calculation performance.
    </p>

    <form action="test.php" method="POST" id="startForm">

        <div class="form-group">
            <label>Examinee Name</label>
            <input
                type="text"
                name="name"
                id="name"
                maxlength="100"
                required
            >
        </div>
		
		<div class="form-group">
    <label>Grade Level</label>

    <select name="grade_level" id="gradeLevel" required>
        <option value="">Select Grade Level</option>

        <option value="Grade 7">Grade 7</option>
        <option value="Grade 8">Grade 8</option>
        <option value="Grade 9">Grade 9</option>
        <option value="Grade 10">Grade 10</option>
        <option value="Grade 11">Grade 11</option>
        <option value="Grade 12">Grade 12</option>
    </select>
</div>

<div class="form-group">
    <label>Section</label>

    <select name="section" id="section" required>
        <option value="">Select Grade First</option>
    </select>
</div>

        <div class="form-group">
            <label>Difficulty</label>

            <select name="difficulty" id="difficulty" required>
                <option value="Easy">Easy</option>
                <option value="Medium">Medium</option>
                <option value="Hard">Hard</option>
            </select>
        </div>
		
		<div class="form-group">
    <label>Operation</label>

    <select name="operation" id="operation" required>
        <option value="Addition">Addition (+)</option>
        <option value="Subtraction">Subtraction (-)</option>
        <option value="Multiplication" selected>Multiplication (×)</option>
        <option value="Division">Division (÷)</option>
    </select>
</div>

        <div class="form-group">
            <label>Number of Questions</label>

            <input
                type="number"
                name="questions"
                id="questions"
                min="10"
                max="50"
                value="10"
                required
            >
        </div>

        <div class="form-group">

            <div class="checkbox-group">
                <input type="checkbox" id="fullscreen">
                <label for="fullscreen">Enable Fullscreen Mode</label>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="sound">
                <label for="sound">Enable Sound Effects</label>
            </div>

        </div>

        <div class="difficulty-info">

            <strong>Difficulty Guide</strong>

            <ul>
                <li><strong>Easy:</strong> New operation every 30 seconds</li>
                <li><strong>Medium:</strong> New operation every 20 seconds</li>
                <li><strong>Hard:</strong> New operation every 10 second</li>
            </ul>

        </div>

        <div class="buttons">

            <button type="submit" class="start-btn">
                Start Test
            </button>

            <a href="leaderboard.php" class="leaderboard-btn">
                View Leaderboard
            </a>

        </div>

    </form>

    <div class="note">
        Accuracy, reaction speed, average thinking time,
        fastest answer, slowest answer, and leaderboard ranking
        will be recorded after completing the test.
    </div>


<div class="logout-container">
    <a href="logout.php" class="logout-btn">
        Logout
    </a>
</div>
<script>

document.getElementById("startForm").addEventListener("submit", function(e){

    const name = document.getElementById("name").value.trim();
    const questions = parseInt(document.getElementById("questions").value);

    if(name.length < 2){
        alert("Please enter your name.");
        e.preventDefault();
        return;
    }

    if(questions < 10 || questions > 50){
        alert("Questions must be between 10 and 50.");
        e.preventDefault();
        return;
    }

    if(document.getElementById("fullscreen").checked){

        if(document.documentElement.requestFullscreen){
            document.documentElement.requestFullscreen();
        }

    }

});
</script>
<script>

const sections = {

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

const gradeSelect =
document.getElementById("gradeLevel");

const sectionSelect =
document.getElementById("section");

gradeSelect.addEventListener("change", function(){

    const grade = this.value;

    sectionSelect.innerHTML =
    '<option value="">Select Section</option>';

    if(sections[grade]){

        sections[grade].forEach(function(section){

            let option =
            document.createElement("option");

            option.value = section;
            option.textContent = section;

            sectionSelect.appendChild(option);

        });

    }

});

</script>
</body>
</html>