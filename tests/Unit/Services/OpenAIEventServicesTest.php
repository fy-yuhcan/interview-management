<?php

namespace Tests\Unit;

use App\Models\Event;
use App\Models\User;
use App\Services\OpenAIEventService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenAI\Laravel\Facades\OpenAI;
use Tests\TestCase;

class OpenAIEventServicesTest extends TestCase
{
    use RefreshDatabase;
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

    public function test_service_create_event_in_db_from_openai_response()
    {
        //openAIからのレスポンスがすでに定義されている体で考える、場合によってはレスポンスがこのようになるか検証必要かも
        $formattedResponse = [
            'title'      => '会議',
            'start_time' => '2025-02-20 10:00:00',
            'end_time'   => '2025-02-20 11:00:00',
            'reservation_time' => '2025-02-20 09:00:00',
            'status'     => '予定',
            'url'        => 'https://example.com',
            'detail'     => 'プロジェクト進捗会議',
        ];

        //json形式に変換
        $fakeAPIresponse = json_encode($formattedResponse);

        //OpenAIEventServiceをインスタンス化
        $service = new OpenAIEventService();

        $user = User::factory()->create();


        //createEventメソッドを実行
        $event = $service->createEvent($fakeAPIresponse,$user->id);

        //データベースにデータが保存されているか確認
        $this->assertDatabaseHas('events', [
            'user_id'    => $user->id,
            'title'      => '会議',
            'start_time' => '2025-02-20 10:00:00',
            'end_time'   => '2025-02-20 11:00:00',
            'reservation_time' => '2025-02-20 09:00:00',
            'status'     => '予定',
            'url'        => 'https://example.com',
            'detail'     => 'プロジェクト進捗会議',
        ]);

        //Eventクラスのインスタンスか確認
        $this->assertInstanceOf(Event::class, $event);
    }
}
