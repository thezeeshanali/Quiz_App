<?php

use App\Http\Controllers\QuizController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('quiz'); // Main quiz view
});

Route::post('/start-quiz', [QuizController::class, 'startQuiz']);
Route::get('/get-random-question', [QuizController::class, 'getRandomQuestion']);
Route::post('/submit-answer', [QuizController::class, 'submitAnswer']);
Route::get('/results', [QuizController::class, 'getResults']);

