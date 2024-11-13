<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quiz</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Basic styling for quiz sections */
        #start-quiz, #question-section, #results-section {
            display: none;
            margin: 20px;
            text-align: center;
        }
        button {
            margin: 5px;
        }
    </style>
</head>
<body>
<!-- Start Quiz Section -->
<div id="start-quiz">
    <h2>Welcome to the Quiz</h2>
    <input type="text" id="username" placeholder="Enter your name">
    <button onclick="startQuiz()">Start Quiz</button>
</div>

<!-- Question Section -->
<div id="question-section">
    <h2 id="question"></h2>
    <div id="answers"></div>
    <button onclick="skipQuestion()">Skip</button>
</div>

<!-- Results Section -->
<div id="results-section">
    <h2>Your Results</h2>
    <p>Correct Answers: <span id="correct-count"></span></p>
    <p>Wrong Answers: <span id="wrong-count"></span></p>
    <p>Skipped Questions: <span id="skipped-count"></span></p>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Setup CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Function to start the quiz
    function startQuiz() {
        const name = $('#username').val();
        if (name) {
            $.post('/start-quiz', { name: name }, function(data) {
                if (data.success) {
                    $('#start-quiz').hide();
                    loadQuestion(); // Load the first question
                }
            }).fail(function() {
                alert("Error starting quiz. Please try again.");
            });
        } else {
            alert("Please enter your name.");
        }
    }

    // Function to load a random question
    function loadQuestion() {
        $.get('/get-random-question', function(data) {
            $('#question-section').show();
            $('#question').text(data.question.text);
            $('#answers').empty();
            $('#answers').data('question-id', data.question.id);

            // Display answers as buttons
            data.answers.forEach(answer => {
                $('#answers').append(`<button onclick="submitAnswer(${data.question.id}, ${answer.id})">${answer.text}</button>`);
            });
        }).fail(function() {
            showResults(); // If no questions left, show results
        });
    }

    // Function to submit an answer
    function submitAnswer(questionId, answerId) {
        $.post('/submit-answer', { question_id: questionId, answer_id: answerId }, function(data) {
            loadQuestion(); // Load the next question after answering
        }).fail(function() {
            alert("Error submitting answer. Please try again.");
        });
    }

    // Function to skip the question
    function skipQuestion() {
        const questionId = $('#answers').data('question-id');
        $.post('/submit-answer', { question_id: questionId, skipped: true }, function(data) {
            loadQuestion(); // Load the next question after skipping
        }).fail(function() {
            alert("Error skipping question. Please try again.");
        });
    }

    // Function to display the results
    function showResults() {
        $('#question-section').hide();
        $('#results-section').show();

        $.get('/results', function(data) {
            $('#correct-count').text(data.correct);
            $('#wrong-count').text(data.wrong);
            $('#skipped-count').text(data.skipped);
        }).fail(function() {
            alert("Error fetching results. Please try again.");
        });
    }

    // Show the start quiz section on page load
    $(document).ready(function() {
        $('#start-quiz').show();
    });
</script>
</body>
</html>
