<?php

namespace Tests\Unit;

use App\Services\OpenAIEventService;
use Illuminate\Support\Facades\Http;
use OpenAI\Laravel\Facades\OpenAI;
use PHPUnit\Framework\TestCase;

class OpenAIEventServicesTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_create_prompt_to_OpenAI(): void
    {
        // テスト用テキスト
        $prompt = '2月10日16時に打ち合わせを行います。';

        //fakeを使ってOpenAI::createPromptメソッドをモック化
        $fakeresponse = [
            'choices' => [
                [
                    'text' => '2月10日16時に打ち合わせを行います。',
                ],
            ],
        ];

        //shouldReceiveで受け取る内容を指定
        OpenAI::shouldReceive('createPrompt')
            ->once()
            ->with($prompt)
            ->andReturn($fakeresponse);
        
        // OpenAI API へのリクエスト
        $response = OpenAI::createPrompt($prompt);

        $this->assertEquals($fakeresponse, $response);

    }

    public function test_service_create_event_in_db_from_openai_response():void
    {
        //openAIからのレスポンスがすでに定義されている体で考える、場合によってはレスポンスがこのようになるか検証必要かも
        $formattedResponse = [
            'title'      => '会議',
            'start_time' => '2025-02-20 10:00:00',
            'end_time'   => '2025-02-20 11:00:00',
            'detail'     => 'プロジェクト進捗会議',
        ];

        //json形式に変換
        $fakeAPIresponse = json_encode($formattedResponse);

        //OpenAIEventServiceをインスタンス化
        $service = new OpenAIEventService();

        //createEventメソッドを実行
        $event = $service->createEvent($fakeAPIresponse);

        //データベースにデータが保存されているか確認
        $this->assertDatabaseHas('events', [
            'title'      => '会議',
            'start_time' => '2025-02-20 10:00:00',
            'end_time'   => '2025-02-20 11:00:00',
            'detail'     => 'プロジェクト進捗会議',
        ]);

        //Eventクラスのインスタンスか確認
        $this->assertInstanceOf(Event::class, $event);
    }
}
