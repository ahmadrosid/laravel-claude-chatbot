<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Anthropic\Anthropic;

class StreamingChatController extends Controller
{
    protected $anthropic;

    public function __construct()
    {
        $headers = [
            'anthropic-version' => '2023-06-01',
            'anthropic-beta' => 'messages-2023-12-15',
            'content-type' => 'application/json',
            'x-api-key' => env('ANTHROPIC_API_KEY')
        ];

        $this->anthropic = Anthropic::factory()
            ->withHeaders($headers)
            ->make();
    }

    private function send($event, $data)
    {
        echo "event: {$event}\n";
        echo 'data: ' . $data;
        echo "\n\n";
        ob_flush();
        flush();
    }

    public function index(Request $request)
    {
        $question = $request->query('question');
        return response()->stream(
            function () use (
                $question
            ) {
                $result_text = "";
                $last_stream_response = null;

                $model = 'claude-3-opus-20240229';
                $max_tokens = 4096;
                $systemMessage = 'You are a helpfull assistant. Answer as concisely as possible.';
                $temperature = 1;
                $messages = [
                    [
                        'role' => 'user',
                        'content' => $question
                    ]
                ];

                $stream = $this->anthropic->chat()->createStreamed([
                    'model' => $model,
                    'temperature' => $temperature,
                    'max_tokens' => $max_tokens,
                    'system' => $systemMessage,
                    'messages' => $messages,
                ]);


                foreach ($stream as $response) {
                    $text = $response->choices[0]->delta->content;
                    if (connection_aborted()) {
                        break;
                    }
                    $data = [
                        'text' => $text,
                    ];
                    $this->send("update", json_encode($data));
                    $result_text .= $text;
                    $last_stream_response = $response;
                }

                $this->send("update", "<END_STREAMING_SSE>");

                logger($last_stream_response->usage->toArray());
            },
            200,
            [
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no',
                'Content-Type' => 'text/event-stream',
            ]
        );
    }

}
