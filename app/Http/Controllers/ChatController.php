<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $payload = [
            'model' => config('services.openai.model', 'gpt-4o-mini'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a helpful assistant for the appointment booking system. Keep replies concise and helpful.',
                ],
                [
                    'role' => 'user',
                    'content' => $data['message'],
                ],
            ],
            'temperature' => 0.4,
        ];

        try {
            $response = Http::withToken(config('services.openai.key'))
                ->post(config('services.openai.endpoint', 'https://api.openai.com/v1/chat/completions'), $payload)
                ->throw()
                ->json();

            $reply = $response['choices'][0]['message']['content'] ?? 'Sorry, I could not generate a reply.';

            return response()->json([
                'reply' => $reply,
            ]);
        } catch (\Throwable $e) {
            Log::error('AI chat error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Unable to get a reply right now.',
            ], 500);
        }
    }
}
