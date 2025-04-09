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
        $expected = [
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
                            'content' => json_encode($expected),
                        ],
                        'finish_reason' => 'stop',
                    ]
                ]
            ])
        ]);

        //インスタンスを生成してメソッドを実行するように修正することでインプットのプロンプトから期待する整形されたレスポンスを作成することを担保するテスト
        $sut = new OpenAIEventService();

        $actual = $sut->getFormattedEventData($prompt);

        //OpenAI::chat()->createメソッドが正常に実行されたか確認
        $this->assertEquals($expected, $actual);
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

        $fakeGoogleEvent = new class {
            public function getId()
            {
                return 'dummy-google-event-id';
            }
        };
        
        //json形式に変換
        $fakeAPIresponse = json_encode($formattedResponse);
        $fakeAPIresponse = json_decode($fakeAPIresponse, true);

        $sut = new EventCreateService();

        $user = User::factory()->create();


        //createEventメソッドを実行
        $actual = $sut->createEvent($fakeAPIresponse, $user->id, $fakeGoogleEvent);

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
