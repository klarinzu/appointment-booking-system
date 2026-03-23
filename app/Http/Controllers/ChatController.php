<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $localReply = $this->buildLocalReply($data['message']);

        if (!config('services.openai.key')) {
            return response()->json([
                'reply' => $localReply,
                'source' => 'documate-local',
            ]);
        }

        $payload = [
            'model' => config('services.openai.model', 'gpt-4o-mini'),
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $this->buildSystemPrompt(),
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
                'source' => 'openai',
            ]);
        } catch (\Throwable $e) {
            Log::error('AI chat error: ' . $e->getMessage());

            return response()->json([
                'reply' => $localReply,
                'source' => 'documate-local',
            ]);
        }
    }

    protected function buildSystemPrompt(): string
    {
        $officeLines = collect(config('documate.office_locations', []))
            ->map(fn (array $office) => '- ' . $office['name'] . ': ' . $office['description'])
            ->implode("\n");

        $transactionLines = collect(config('documate.transaction_types', []))
            ->take(28)
            ->map(fn (array $transaction) => '- ' . $transaction['code'] . ': ' . $transaction['name'])
            ->implode("\n");

        $handbookSummary = collect(config('documate.handbook.sections', []))
            ->map(fn (array $section) => '- ' . $section['title'] . ': ' . $section['body'])
            ->implode("\n");

        $appointmentRules = 'Appointments are required for all forms after approval. Capacity is 50 per day: 25 in the morning and 25 in the afternoon.';

        return "You are the DOCUMATE assistant for VPSD transactions.\n"
            . "Help students with office locations, form workflows, signatories, appointment scheduling, clearance reminders, and handbook guidance.\n"
            . "Keep answers concise, practical, and specific.\n"
            . "Appointment policy: {$appointmentRules}\n"
            . "Office directory:\n{$officeLines}\n"
            . "Transaction catalog:\n{$transactionLines}\n"
            . "Handbook notes:\n{$handbookSummary}";
    }

    protected function buildLocalReply(string $message): string
    {
        $needle = Str::lower($message);
        $transactions = collect(config('documate.transaction_types', []));
        $offices = collect(config('documate.office_locations', []));
        $handbook = collect(config('documate.handbook.sections', []));

        if (preg_match('/f-sdm-\d{3}/i', $message, $matches)) {
            $transaction = $transactions->firstWhere('code', Str::upper($matches[0]));

            if ($transaction) {
                return $this->formatTransactionReply($transaction);
            }
        }

        $matchedTransaction = $transactions->first(function (array $transaction) use ($needle) {
            $haystack = Str::lower($transaction['name'] . ' ' . $transaction['short_name'] . ' ' . $transaction['slug']);
            $tokens = collect(explode(' ', $needle))
                ->filter(fn ($word) => Str::length($word) > 3)
                ->values();

            return Str::contains($haystack, $needle)
                || ($tokens->isNotEmpty() && $tokens->every(fn ($word) => Str::contains($haystack, $word)));
        });

        if ($matchedTransaction) {
            return $this->formatTransactionReply($matchedTransaction);
        }

        $matchedOffice = $offices->first(function (array $office, string $key) use ($needle) {
            return Str::contains($needle, [$key, Str::lower($office['name'])]);
        });

        if ($matchedOffice) {
            return $matchedOffice['name'] . ': ' . $matchedOffice['description'];
        }

        if (Str::contains($needle, ['clearance', 'hold', 'cleared', 'tagging'])) {
            return 'Clearance status affects transaction eligibility in DOCUMATE. A clearance hold blocks most non-clearance requests until a student officer or administrator updates your record.';
        }

        if (Str::contains($needle, ['appointment', 'schedule', 'morning', 'afternoon', 'slot'])) {
            return 'DOCUMATE appointments are available for all forms. Each day accommodates 50 students: 25 in the morning and 25 in the afternoon. Book your appointment from the transaction detail page after approval.';
        }

        if (Str::contains($needle, ['status', 'progress', 'track'])) {
            $statusText = collect(config('documate.statuses', []))
                ->map(fn (string $label, string $key) => $label . ' (' . $key . ')')
                ->implode(', ');

            return 'DOCUMATE tracks requests through these stages: ' . $statusText . '. You can monitor the current stage from the transaction detail page.';
        }

        if (Str::contains($needle, ['handbook', 'student handbook', 'policy'])) {
            $section = $handbook->first();

            return 'The Student Handbook is available inside DOCUMATE. Start with "' . ($section['title'] ?? 'Student Conduct') . '" for core guidance, then review the handbook page for the rest of the policy sections.';
        }

        if (Str::contains($needle, ['where', 'office', 'signatory', 'signature', 'notary', 'vpsd', 'guidance', 'dean', 'vpas'])) {
            $officeList = $offices->map(fn (array $office) => $office['name'])->implode(', ');

            return 'DOCUMATE office guidance is available for these locations: ' . $officeList . '. Ask me about a specific office or transaction form and I will point you to the right signatory.';
        }

        $topTransactions = $transactions->take(5)->map(fn (array $transaction) => $transaction['code'])->implode(', ');

        return 'I can help with DOCUMATE transaction steps, required signatories, office guidance, clearance reminders, and handbook questions. Try asking about a form code like '
            . $topTransactions . ' or a topic like VPSD, notary, or clearance status.';
    }

    protected function formatTransactionReply(array $transaction): string
    {
        $steps = collect($transaction['workflow_steps'] ?? [])
            ->take(3)
            ->map(fn (string $step, int $index) => ($index + 1) . '. ' . $step)
            ->implode(' ');

        $signatories = collect($transaction['required_signatories'] ?? [])->implode(', ');

        return $transaction['code'] . ' - ' . $transaction['name']
            . '. Required signatories: ' . ($signatories ?: 'None listed')
            . '. Main steps: ' . $steps
            . ' Final step: book your DOCUMATE appointment and bring the completed form on your scheduled session.';
    }
}
