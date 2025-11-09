<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\QuestionPackage;
use App\Models\PackageQuestion;

class QuestionSeeder extends Seeder
{
    public function run()
    {
        $package = QuestionPackage::first();
        $orderNumber = 1;

        // Listening Questions
        for ($i = 1; $i <= 15; $i++) {
            $question = Question::create([
                'section' => 'listening',
                'question' => "Listen to the audio and choose the best answer for question {$i}.",
                'option_a' => 'Option A for listening question ' . $i,
                'option_b' => 'Option B for listening question ' . $i,
                'option_c' => 'Option C for listening question ' . $i,
                'option_d' => 'Option D for listening question ' . $i,
                'correct_answer' => ['a', 'b', 'c', 'd'][rand(0, 3)],
                'audio_file' => 'demo_audio_' . $i . '.mp3',
                'explanation' => 'Explanation for listening question ' . $i,
                'is_active' => true,
            ]);

            PackageQuestion::create([
                'package_id' => $package->id,
                'question_id' => $question->id,
                'order_number' => $orderNumber++,
            ]);
        }

        // Structure Questions
        for ($i = 1; $i <= 30; $i++) {
            $question = Question::create([
                'section' => 'structure',
                'question' => "Choose the best answer to complete the sentence for question {$i}. The student _____ his homework yesterday.",
                'option_a' => 'complete',
                'option_b' => 'completed',
                'option_c' => 'completing',
                'option_d' => 'completes',
                'correct_answer' => 'b',
                'explanation' => 'The correct answer is "completed" because it indicates past tense.',
                'is_active' => true,
            ]);

            PackageQuestion::create([
                'package_id' => $package->id,
                'question_id' => $question->id,
                'order_number' => $orderNumber++,
            ]);
        }

        // Reading Questions
        for ($i = 1; $i <= 30; $i++) {
            $question = Question::create([
                'section' => 'reading',
                'question' => "Based on the passage, what is the main idea of paragraph {$i}?",
                'passage' => "This is a sample reading passage for question {$i}. The passage discusses various topics related to academic subjects including science, history, literature, and technology. Students are expected to comprehend the main ideas, supporting details, and inferences from the given text. The passage provides comprehensive information that helps students understand complex academic concepts and improve their reading comprehension skills.",
                'option_a' => 'The passage discusses scientific concepts',
                'option_b' => 'The passage focuses on historical events',
                'option_c' => 'The passage covers academic subjects and comprehension',
                'option_d' => 'The passage is about technology advancement',
                'correct_answer' => 'c',
                'explanation' => 'The main idea is about academic subjects and reading comprehension.',
                'is_active' => true,
            ]);

            PackageQuestion::create([
                'package_id' => $package->id,
                'question_id' => $question->id,
                'order_number' => $orderNumber++,
            ]);
        }
    }
}
