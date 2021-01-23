<?php
session_start();

include "generate_questions.php";
generateQuestions();

const CURRENT_QUESTION_INFO = "Question #%s of #%s";

if (!isset($_SESSION['correctAnswer'])) {
    $_SESSION['correctAnswer'] = null;
}

if (!isset($_SESSION['totalCorrect'])) {
    $_SESSION['totalCorrect'] = 0;
}

if (!isset($_SESSION['askedQuestions'])) {
    $_SESSION['askedQuestions'] = [];
}

if (!isset($_SESSION['currentQuestion'])) {
    $_SESSION['currentQuestion'] = 1;
}

$_SESSION['toastMessage'] = '';

if (isset($_POST["answer"])) {
    if ((string)$_POST["answer"] === (string)$_SESSION['correctAnswer']) {
        $_SESSION['toastMessage'] = 'Correct!';
        ++$_SESSION['totalCorrect'];
    } else {
        $_SESSION['toastMessage'] = 'Sorry, incorrect.';
    }

    if (!isset($_SESSION['currentQuestion'])) {
        $_SESSION['currentQuestion'] = 1;
    } else {
        ++$_SESSION['currentQuestion'];
    }
}

if (isset($_POST["reset"])) {
    session_destroy();
    header("Refresh:0");
}

function showView()
{
    if (count($_SESSION['askedQuestions']) === count($_SESSION['questions'])) {
        showScore();
    } else {
        showQuestion();
    }
}

function showScore()
{
    $scorePercentage = ((int)$_SESSION['totalCorrect'] / TOTAL_NUMBER_OF_QUESTIONS) * 100;
    echo "<p class='quiz'>You scored " . $_SESSION['totalCorrect'] . " out of " . TOTAL_NUMBER_OF_QUESTIONS . "</p>";
    echo "<p class='quiz'>$scorePercentage%</p>";
    echo '<form action="index.php" method="post">
            <input type="submit" class="btn" name="reset" value="Reset" />
        ';
}

function showQuestion()
{
    $randomQuestionIndex = null;
    $currentQuestion = null;
    while (!in_array($randomQuestionIndex, $_SESSION['askedQuestions'], true)) {
        $randomQuestionIndex = array_rand($_SESSION['questions']);
        $currentQuestion = $_SESSION['questions'][$randomQuestionIndex];
        $_SESSION['correctAnswer'] = $currentQuestion['correctAnswer'];
        $_SESSION['askedQuestions'][] = $randomQuestionIndex;
    }

    echo '<p class="breadcrumbs">' . sprintf(CURRENT_QUESTION_INFO, $_SESSION["currentQuestion"],
            TOTAL_NUMBER_OF_QUESTIONS) . '</p>';

    echo '
        <p class="quiz">What is ' . $currentQuestion['firstNumber'] . ' + ' . $currentQuestion['secondNumber'] . '?</p>
        <form action="index.php" method="post">
            <input type="hidden" name="id" value="0" />';

    foreach ($currentQuestion['options'] as $option) {
        echo '<input type="submit" class="btn" name="answer" value="' . $option . '" />';
    }

    echo '</form>';

    echo '
            <p>' . $_SESSION['toastMessage'] . '</p>
        ';
}