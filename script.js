"use strict";

/* ==========================================
   Global Variables
========================================== */

let streak = 0;
let bestStreak = 0;

let soundEnabled = false;

/* ==========================================
   Sound Effects
========================================== */

const correctSound = new Audio(
    "assets/sounds/correct.mp3"
);

const wrongSound = new Audio(
    "assets/sounds/wrong.mp3"
);

function playCorrectSound(){

    if(soundEnabled){

        correctSound.currentTime = 0;
        correctSound.play();

    }

}

function playWrongSound(){

    if(soundEnabled){

        wrongSound.currentTime = 0;
        wrongSound.play();

    }

}

/* ==========================================
   Sound Toggle
========================================== */

function initializeSoundToggle(){

    const soundCheckbox =
    document.getElementById("sound");

    if(!soundCheckbox) return;

    soundCheckbox.addEventListener(
        "change",
        function(){

            soundEnabled =
            this.checked;

            localStorage.setItem(
                "soundEnabled",
                soundEnabled
            );

        }
    );

    const saved =
    localStorage.getItem(
        "soundEnabled"
    );

    if(saved === "true"){

        soundCheckbox.checked = true;
        soundEnabled = true;

    }

}

/* ==========================================
   Fullscreen
========================================== */

function enterFullscreen(){

    const el =
    document.documentElement;

    if(el.requestFullscreen){

        el.requestFullscreen();

    }

}

function exitFullscreen(){

    if(document.exitFullscreen){

        document.exitFullscreen();

    }

}

/* ==========================================
   Streak System
========================================== */

function increaseStreak(){

    streak++;

    if(streak > bestStreak){

        bestStreak = streak;

    }

    updateStreakDisplay();
}

function resetStreak(){

    streak = 0;

    updateStreakDisplay();
}

function updateStreakDisplay(){

    const streakElement =
    document.getElementById("streak");

    if(!streakElement) return;

    streakElement.innerHTML =
        "🔥 Streak: " + streak;
}

/* ==========================================
   Stopwatch Helpers
========================================== */

function formatSeconds(seconds){

    return Number(seconds)
        .toFixed(2) + "s";

}

function average(array){

    if(array.length === 0){

        return 0;

    }

    return (
        array.reduce(
            (a,b)=>a+b,
            0
        )
        /
        array.length
    );

}

function fastest(array){

    if(array.length === 0){

        return 0;

    }

    return Math.min(...array);

}

function slowest(array){

    if(array.length === 0){

        return 0;

    }

    return Math.max(...array);

}

/* ==========================================
   Accuracy
========================================== */

function calculateAccuracy(
    correct,
    total
){

    if(total === 0){

        return 0;

    }

    return (
        (correct / total) * 100
    ).toFixed(2);

}

/* ==========================================
   Rating System
========================================== */

function getRating(
    accuracy
){

    accuracy =
    Number(accuracy);

    if(accuracy >= 95){

        return "Genius Level";

    }

    if(accuracy >= 85){

        return "Excellent";

    }

    if(accuracy >= 70){

        return "Above Average";

    }

    if(accuracy >= 50){

        return "Average";

    }

    return "Needs Practice";
}

/* ==========================================
   Local Personal Best
========================================== */

function savePersonalBest(
    accuracy
){

    let best =
    localStorage.getItem(
        "bestAccuracy"
    );

    if(
        best === null ||
        Number(accuracy) >
        Number(best)
    ){

        localStorage.setItem(
            "bestAccuracy",
            accuracy
        );

    }

}

function getPersonalBest(){

    return (
        localStorage.getItem(
            "bestAccuracy"
        ) || 0
    );

}

/* ==========================================
   Anti Refresh Warning
========================================== */

function enableRefreshProtection(){

    window.addEventListener(
        "beforeunload",
        function(e){

            e.preventDefault();

            e.returnValue =
                "Your test may be lost.";

        }
    );

}

/* ==========================================
   Countdown Utility
========================================== */

function startCountdown(
    elementId,
    callback
){

    let value = 3;

    const element =
    document.getElementById(
        elementId
    );

    if(!element){

        return;
    }

    element.textContent =
    value;

    const interval =
    setInterval(()=>{

        value--;

        if(value > 0){

            element.textContent =
            value;

        }
        else if(value === 0){

            element.textContent =
            "GO!";

        }
        else{

            clearInterval(
                interval
            );

            if(
                typeof callback ===
                "function"
            ){

                callback();

            }

        }

    },1000);

}

/* ==========================================
   Difficulty Time Limit
========================================== */

function getDifficultyTimeLimit(
    difficulty
){

    switch(difficulty){

        case "Easy":
            return 5;

        case "Medium":
            return 3;

        case "Hard":
            return 1;

        default:
            return 5;
    }

}

/* ==========================================
   Auto Advance Timer
========================================== */

function createQuestionTimer(
    seconds,
    onExpire
){

    return setTimeout(
        function(){

            if(
                typeof onExpire ===
                "function"
            ){

                onExpire();

            }

        },
        seconds * 1000
    );

}

/* ==========================================
   Chart.js Helpers
========================================== */

function buildAccuracyChart(
    canvasId,
    labels,
    values
){

    if(
        typeof Chart ===
        "undefined"
    ){

        return;
    }

    const ctx =
    document.getElementById(
        canvasId
    );

    if(!ctx){

        return;
    }

    new Chart(ctx,{

        type:"bar",

        data:{
            labels:labels,

            datasets:[{
                label:"Accuracy %",
                data:values
            }]
        },

        options:{
            responsive:true
        }

    });

}

/* ==========================================
   Initialize
========================================== */

document.addEventListener(
    "DOMContentLoaded",
    function(){

        initializeSoundToggle();

    }
);