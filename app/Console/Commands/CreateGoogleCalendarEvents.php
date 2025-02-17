<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\EventCreateService;
use App\Services\GoogleCalendarService;
use Illuminate\Console\Command;

class CreateGoogleCalendarEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-google-calendar-events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'googleカレンダーからイベントを取得してDBに保存する';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // tokenがあるユーザーを取得
        $users = User::WhereNotNull('token')->get();
        
        // イベントを作成するサービスクラス
        $createdEvents = new EventCreateService();

        // ユーザーごとにイベントを取得してDBに保存
        foreach ($users as $user) {
            $service = new GoogleCalendarService($user->token);
            $events = $service->getFormattedEvents();
    
            foreach ($events as $data) {
                $createdEvents->createEvent($data, $user->id);
            }
        }
    }
}
