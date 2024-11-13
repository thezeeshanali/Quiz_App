<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Question;
use App\Models\Answer;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class QuizController extends Controller
{
    public function startQuiz(Request $request)
    {
        // Store user's name and create a new user entry
        $user = User::create(['name' => $request->name]);
        Session::put('user_id', $user->id);

        return response()->json(['success' => true]);
    }

    public function getRandomQuestion()
    {
        // Fetch a random question that hasn't been answered by this user
        $userId = Session::get('user_id');
        $answeredQuestionIds = Result::where('user_id', $userId)->pluck('question_id')->toArray();

        $question = Question::whereNotIn('id', $answeredQuestionIds)->inRandomOrder()->first();
        if ($question) {
            $answers = $question->answers()->inRandomOrder()->get();
            return response()->json(['question' => $question, 'answers' => $answers]);
        }

        return response()->json(['message' => 'No more questions'], 404);
    }

    public function submitAnswer(Request $request)
    {
        $userId = Session::get('user_id');
        $status = 'wrong';

        $answer = Answer::find($request->answer_id);
        if ($answer && $answer->is_correct) {
            $status = 'correct';
        } elseif ($request->skipped) {
            $status = 'skipped';
        }

        Result::create([
            'user_id' => $userId,
            'question_id' => $request->question_id,
            'answer_id' => $request->answer_id,
            'status' => $status
        ]);

        return response()->json(['success' => true]);
    }

    public function getResults()
    {
        $userId = Session::get('user_id');
        $correctCount = Result::where('user_id', $userId)->where('status', 'correct')->count();
        $wrongCount = Result::where('user_id', $userId)->where('status', 'wrong')->count();
        $skippedCount = Result::where('user_id', $userId)->where('status', 'skipped')->count();

        return response()->json([
            'correct' => $correctCount,
            'wrong' => $wrongCount,
            'skipped' => $skippedCount
        ]);
    }
}

