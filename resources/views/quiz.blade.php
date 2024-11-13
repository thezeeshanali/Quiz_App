<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quiz</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Styling */
        body {
            background-color: #87CEEB; /* Sky blue background */
            font-family: Arial, sans-serif;
            color: #333;
        }
        #start-quiz, #question-section, #results-section {
            display: none;
            max-width: 500px;
            margin: 40px auto;
            padding: 20px;
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        h2 {
            color: #333;
        }
        /* Center the Welcome and Results titles */
        .center-text {
            text-align: center;
        }
        /* Left align the question and options */
        .left-align {
            text-align: left;
        }
        .question-number {
            font-weight: bold;
            margin-bottom: 5px;
            text-align: left;
        }
        button, input[type="radio"] {
            margin: 10px;
            padding: 10px 20px;
            border: none;
            background-color: #888;
            color: white;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #555;
        }
        #answers label {
            display: flex;
            align-items: center;
            text-align: left; /* Ensure labels are left-aligned */
            margin: 8px 0;
            font-size: 18px;
        }
        #answers input[type="radio"] {
            margin-right: 10px; /* Space between radio button and text */
        }
        .hidden {
            display: none;
        }
    </style>

</head>
<body>
<!-- Start Quiz Section -->
<div id="start-quiz" class="center-text">
    <h2>Welcome to the Quiz</h2>
    <input type="text" id="username" placeholder="Enter your name">
    <button onclick="startQuiz()">Start Quiz</button>
</div>

<!-- Question Section -->
<div id="question-section">
    <div class="question-number" id="question-number"></div> <!-- Display question number here -->
    <h2 class="left-align" id="question"></h2>
    <form id="answers" class="left-align">
        <!-- Radio button options will be dynamically added here -->
    </form>
    <button onclick="skipQuestion()">Skip</button>
    <button onclick="submitAnswer()" id="next-button" class="hidden">Next</button>
</div>

<!-- Results Section -->
<div id="results-section" class="center-text">
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

    let selectedAnswerId = null;
    let currentQuestionNumber = 1; // Track the current question number

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
        $('#next-button').addClass('hidden'); // Hide Next button
        selectedAnswerId = null;
        $.get('/get-random-question', function(data) {
            $('#question-section').show();

            // Display question number and question text
            $('#question-number').text(`Question ${currentQuestionNumber}`);
            $('#question').text(data.question.text);
            $('#answers').empty().data('question-id', data.question.id);

            // Render options with radio buttons, ensuring HTML tags are displayed
            data.answers.forEach(answer => {
                $('#answers').append(`
                        <label>
                            <input type="radio" name="answer" value="${answer.id}" onclick="selectAnswer(${answer.id})">
                            ${$('<div>').text(answer.text).html()}
                        </label>
                    `);
            });
        }).fail(function() {
            showResults(); // If no questions left, show results
        });
    }

    // Function to handle answer selection
    function selectAnswer(answerId) {
        selectedAnswerId = answerId;
        $('#next-button').removeClass('hidden'); // Show Next button after selecting an answer
    }

    // Function to submit an answer
    function submitAnswer() {
        if (selectedAnswerId) {
            const questionId = $('#answers').data('question-id');
            $.post('/submit-answer', { question_id: questionId, answer_id: selectedAnswerId }, function(data) {
                currentQuestionNumber++; // Increment question number
                loadQuestion(); // Load the next question after answering
            }).fail(function() {
                alert("Error submitting answer. Please try again.");
            });
        }
    }

    // Function to skip the question
    function skipQuestion() {
        const questionId = $('#answers').data('question-id');
        $.post('/submit-answer', { question_id: questionId, skipped: true }, function(data) {
            currentQuestionNumber++; // Increment question number
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
