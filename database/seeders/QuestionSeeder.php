<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\Answer;

class QuestionSeeder extends Seeder
{
    public function run()
    {
        // Array of questions with possible answers and the correct answer ID
        $questions = [
            [
                'text' => 'What does HTML stand for?',
                'answers' => [
                    ['text' => 'Hyper Text Markup Language', 'is_correct' => true],
                    ['text' => 'Home Tool Markup Language', 'is_correct' => false],
                    ['text' => 'Hyperlinks and Text Markup Language', 'is_correct' => false],
                    ['text' => 'Hyper Trainer Marking Language', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Who is making the Web standards?',
                'answers' => [
                    ['text' => 'Mozilla', 'is_correct' => false],
                    ['text' => 'Microsoft', 'is_correct' => false],
                    ['text' => 'The World Wide Web Consortium', 'is_correct' => true],
                    ['text' => 'Google', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Choose the correct HTML element for the largest heading:',
                'answers' => [
                    ['text' => '<heading>', 'is_correct' => false],
                    ['text' => '<h6>', 'is_correct' => false],
                    ['text' => '<h1>', 'is_correct' => true],
                    ['text' => '<head>', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'What is the correct HTML element for inserting a line break?',
                'answers' => [
                    ['text' => '<lb>', 'is_correct' => false],
                    ['text' => '<break>', 'is_correct' => false],
                    ['text' => '<br>', 'is_correct' => true],
                    ['text' => '<line>', 'is_correct' => false],
                ]
            ],
            [
                'text' => 'Which character is used to indicate an end tag?',
                'answers' => [
                    ['text' => '*', 'is_correct' => false],
                    ['text' => '/', 'is_correct' => true],
                    ['text' => '<', 'is_correct' => false],
                    ['text' => '^', 'is_correct' => false],
                ]
            ],
        ];

        // Insert each question and its answers
        foreach ($questions as $questionData) {
            $question = Question::create(['text' => $questionData['text']]);
            foreach ($questionData['answers'] as $answerData) {
                Answer::create([
                    'question_id' => $question->id,
                    'text' => $answerData['text'],
                    'is_correct' => $answerData['is_correct'],
                ]);
            }
        }
    }
}
