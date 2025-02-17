<?php

namespace App\Services;

use App\Models\Event;
use App\Notifications\EventCreatedNotification;
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
        $user = Auth::user();

        $formattedResponse = $this->getFormattedEventData($prompt);

        // EventCreateService クラスのインスタンスを生成して createEvent メソッドを呼び出して登録
        $createEventService = new EventCreateService();

        $event = $createEventService->createEvent($formattedResponse, $user->id);

        // イベント作成通知を送信
        $user->notify(new EventCreatedNotification($event));

        return $event;
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
        $contentText = $response['choices'][0]['message']['content'] ?? '';

        $jsonText = $this->extractJsonFromResponse($contentText);

        // レスポンスを配列に変換
        $formattedResponse = json_decode($jsonText, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("JSON decode error: " . json_last_error_msg());
        }

        return $formattedResponse;
    }

    /**
     * レスポンスから JSON 部分のみを抽出する
     *
     * @param string $contentText
     * @return string
     */
    private function extractJsonFromResponse($contentText)
    {

        // もし $jsonText が文字列でなければ、エンコードして文字列に変換
        if (!is_string($contentText)) {
            $jsonText = json_encode($contentText);
        }

        // もしコードブロックなどが含まれていたら、正規表現で JSON 部分のみを抽出
        if (preg_match('/```json\s*(\{.*\})\s*```/s', $contentText, $matches)) {
            $contentText = $matches[1];
        }

        $jsonText = trim($contentText);

        return $jsonText;
    }
}
