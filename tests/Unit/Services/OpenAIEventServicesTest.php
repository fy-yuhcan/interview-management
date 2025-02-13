<?php

namespace Tests\Unit;

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
}
