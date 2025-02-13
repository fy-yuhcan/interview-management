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
        return $this->createEvent($formattedResponse,$userId);
    }

    /**
     * OpenAI API にリクエストを送信し、整形されたデータを取得する
     *
     * @param string $prompt
     * @return array
     */
    private function getFormattedEventData($prompt)
    {
        // OpenAI API へのリクエスト
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a calendar app. A user asks you to schedule a meeting at a specific time. The user says: "Schedule a meeting for me at 2pm on Tuesday."'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ]
        ]);

        // OpenAIのレスポンスを整形
        $jsonText = $response['choices'][0]['message']['content'] ?? '';

        // レスポンスが空の場合はエラーを返す
        if (empty($jsonText)) {
            return response()->json(['error' => 'Failed to get response from OpenAI'], 500);
        }

        // レスポンスを配列に変換
        $formattedResponse = json_decode($jsonText, true);

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
        //$formattedResponseを連想配列に変換
        $formattedResponse = json_decode($formattedResponse, true);

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
