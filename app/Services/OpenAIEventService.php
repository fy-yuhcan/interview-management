<?php

namespace App\Services;

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
        $formattedResponse = $this->getFormattedEventData($prompt);
        return $this->createEvent($formattedResponse);
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
            'model' => 'gpt-3.5-turbo',
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
     * @param array $formattedResponse
     * @return void
     */
    private function createEvent($formattedResponse)
    {
        // ログインユーザーのIDを取得
        $userId = Auth::id();
        if (empty($userId)) {
            return response()->json(['error' => 'Failed to get user ID'], 500);
        }
        
        $event = Event::create([
            'user_id' => $userId,
            'title' => $formattedResponse['title'],
            'start_time' => $formattedResponse['start_time'],
            'end_time' => $formattedResponse['end_time'],
            'detail' => $formattedResponse['detail'],
        ]);

        return $event;
    }
    
}