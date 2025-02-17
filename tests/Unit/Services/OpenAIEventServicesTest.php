<?php

namespace Tests\Unit;

use App\Models\Event;
use App\Models\User;
use App\Services\EventCreateService;
use App\Services\OpenAIEventService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use Tests\TestCase;

class OpenAIEventServicesTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // テスト環境用に OpenAI API キーを設定
        config(['openai.api_key' => 'test-api-key']);
    }

    /**
     * A basic unit test example.
     */
    public function test_create_prompt_to_OpenAI(): void
    {
        // テスト用テキスト
        $prompt = '2月10日16時に打ち合わせを行います。';

        //fakeを使ってOpenAI::createPromptメソッドをモック化
        $fakeAPIresponse = [
            'title'      => '会議',
            'start_time' => '2025-02-20 10:00:00',
            'end_time'   => '2025-02-20 11:00:00',
            'reservation_time' => '2025-02-20 09:00:00',
            'status'     => '予定',
            'url'        => 'https://example.com',
            'detail'     => 'プロジェクト進捗会議',
        ];

        //fakeを使ってOpenAI::chat()をモック化
        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'index' => 0,
                        'message' => [
                            'role' => 'assistant',
                            'content' => json_encode($fakeAPIresponse),
                        ],
                        'finish_reason' => 'stop',
                    ]
                ]
            ])
        ]);

        //OpenAI::chat()->createメソッドを実行
        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'prompt' => $prompt,
        ]);

        //OpenAI::chat()->createメソッドが正常に実行されたか確認
        $this->assertEquals(json_encode($fakeAPIresponse), $response['choices'][0]['message']['content']);
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
        $fakeAPIresponse = json_decode($fakeAPIresponse, true);

        $sut = new EventCreateService();

        $user = User::factory()->create();


        //createEventメソッドを実行
        $actual = $sut->createEvent($fakeAPIresponse, $user->id);

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
        $this->assertInstanceOf(Event::class, $actual);
    }
}
