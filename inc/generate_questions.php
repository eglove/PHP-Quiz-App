<?php
const TOTAL_NUMBER_OF_QUESTIONS = 10;
const NUMBER_OF_OPTIONS_PER_QUESTION = 3;
const OPTION_RANGE = 10;
const QUESTION_MIN_RANGE = 0;
const QUESTION_MAX_RANGE = 100;
const MESSAGE_FAILED_TO_GENERATE_RANDOM_INTEGER = "Failed to generate random integer between %s and %s. Exception: %s";

$_SESSION["questions"] = [];

function generateQuestions()
{
    $questions = [];
    for ($i = 0; $i < TOTAL_NUMBER_OF_QUESTIONS; $i++) {
        $questions[$i]['firstNumber'] = getRandomInteger(QUESTION_MIN_RANGE, QUESTION_MAX_RANGE);
        $questions[$i]['secondNumber'] = getRandomInteger(QUESTION_MIN_RANGE, QUESTION_MAX_RANGE);
        $questions[$i]['correctAnswer'] = $questions[$i]['firstNumber'] + $questions[$i]['secondNumber'];
        $questions[$i]['options'] = getAnswerOptions(NUMBER_OF_OPTIONS_PER_QUESTION, $questions[$i]['correctAnswer']);
    }

    $_SESSION["questions"] = $questions;
}

function getAnswerOptions(int $numberOfOptions, int $correctAnswer): array
{
    $options = [$correctAnswer];

    while (count($options) !== $numberOfOptions) {
        $option = getRandomInteger($correctAnswer - OPTION_RANGE, $correctAnswer + OPTION_RANGE);

        if (!in_array($option, $options, true)) {
            $options[] = $option;
        }
    }

    shuffle($options);
    return $options;
}

function getRandomInteger(int $min, int $max): ?int
{
    try {
        return random_int($min, $max);
    } catch (Exception $e) {
        echo sprintf(MESSAGE_FAILED_TO_GENERATE_RANDOM_INTEGER, $min, $max, $e);
        return null;
    }
}