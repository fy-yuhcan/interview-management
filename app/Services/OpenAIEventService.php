<?php

namespace App\Services;

use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use OpenAI\Laravel\Facades\OpenAI;

class OpenAIEventService
{
    /**
     * OpenAI API にリクエストを送信し、整形されたデータを取得し、DBに保存する
     *
     * @param string $prompt
     * @return \App\Models\Event
     */
    public function createEventFromPrompt($prompt)
    {
        $userId = Auth::id();
        $formattedResponse = $this->getFormattedEventData($prompt);
        return $this->createEvent($formattedResponse, $userId);
    }

    /**
     * OpenAI API にリクエストを送信し、整形されたデータを取得する
     *
     * @param string $prompt
     * @return array
     */
    private function getFormattedEventData($prompt)
    {
        $systemMessage = 'You are a calendar app. A user will provide a scheduling request in natural language. ' .
        'Please return a valid JSON object with the following keys: ' .
        '"title", "start_time", "end_time", "reservation_time", "status", "url", "detail". ' .
        'The start_time and end_time should be in the format "YYYY-MM-DD HH:MM:SS". ' .
        'Do not include any extra text or commentary. Only output valid JSON.';

        // OpenAI API へのリクエスト
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $systemMessage
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ]
        ]);

        // OpenAIのレスポンスを整形
        $jsonText = $response['choices'][0]['message']['content'] ?? '';

        // もし $jsonText が文字列でなければ、エンコードして文字列に変換
        if (!is_string($jsonText)) {
            $jsonText = json_encode($jsonText);
        }

        // もしコードブロックなどが含まれていたら、正規表現で JSON 部分のみを抽出
        if (preg_match('/```json\s*(\{.*\})\s*```/s', $jsonText, $matches)) {
            $jsonText = $matches[1];
        }

        $jsonText = trim($jsonText);

        // レスポンスが空の場合はエラーを返す
        if (empty($jsonText)) {
            return response()->json(['error' => 'Failed to get response from OpenAI'], 500);
        }

        // レスポンスを配列に変換
        $formattedResponse = json_decode($jsonText, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("JSON decode error: " . json_last_error_msg());
        }

        return $formattedResponse;
    }

    /**
     * OpenAI API から取得したデータをDBに保存する
     *
     * @param string $formattedResponse
     * @return void
     */
    public function createEvent($formattedResponse, $userId)
    {
        $event = Event::create([
            'user_id' => $userId,
            'title' => $formattedResponse['title'],
            'start_time' => $formattedResponse['start_time'],
            'end_time' => $formattedResponse['end_time'],
            'reservation_time' => $formattedResponse['reservation_time'],
            'status' => $formattedResponse['status'],
            'url' => $formattedResponse['url'],
            'detail' => $formattedResponse['detail'],
        ]);

        return $event;
    }
}
