<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HelpController extends Controller
{
    public function show()
    {
        return view('client.help');
    }

    public function ask(Request $request)
    {
        $request->validate([
            'question' => 'required|string|max:5000'
        ]);
        

        $apiKey = config('services.gemini.api_key') ?: env('GEMINI_API_KEY');
        $model = env('GEMINI_MODEL', 'gemini-2.0-flash');
        $question = $request->input('question');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'error' => 'Gemini API key is not configured. Please add GEMINI_API_KEY to your .env file.'
            ], 500);
        }

        // Retry up to 3 times with 2 second delays
        $maxRetries = 3;
        $retryDelay = 2;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = Http::timeout(30)
                    ->post("https://generativelanguage.googleapis.com/v1/models/{$model}:generateContent?key={$apiKey}", [
                        'contents' => [
                            [
                                'parts' => [
                                    ['text' => $question]
                                ]
                            ]
                        ]
                    ]);

                if ($response->successful()) {
                    $data = $response->json();

                    // SAFE extraction
                    $answer = null;

                    if (
                        isset($data['candidates']) &&
                        count($data['candidates']) > 0 &&
                        isset($data['candidates'][0]['content']['parts']) &&
                        count($data['candidates'][0]['content']['parts']) > 0 &&
                        isset($data['candidates'][0]['content']['parts'][0]['text'])
                    ) {
                        $answer = $data['candidates'][0]['content']['parts'][0]['text'];
                    }

                    if (!$answer) {
                        return response()->json([
                            'success' => false,
                            'error' => 'Gemini returned no content (possibly blocked or overloaded)'
                        ], 503);
                    }

                    return response()->json([
                        'success' => true,
                        'answer' => $answer
                    ]);

                }
                
                // If 503 and not last attempt, retry
                if ($response->status() === 503 && $attempt < $maxRetries) {
                    sleep($retryDelay);
                    continue;
                }
                
                return response()->json([
                    'success' => false,
                    'error' => 'API request failed: ' . $response->body()
                ], $response->status());


                
            } catch (\Exception $e) {
                if ($attempt < $maxRetries) {
                    sleep($retryDelay);
                    continue;
                }
                
                return response()->json([
                    'success' => false,
                    'error' => 'Error: ' . $e->getMessage()
                ], 500);
            }
        }
    }
}
