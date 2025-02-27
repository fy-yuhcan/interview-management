<?php

namespace App\Livewire;

use App\Services\OpenAIEventService;
use Livewire\Component;

class Input extends Component
{
    //wire:modelでバインドするプロパティ
    public $prompt;

    public function render()
    {
        return view('livewire.input');
    }

    public function submit()
    {
        try { 
            $service = new OpenAIEventService();
            $response = $service->createEventFromPrompt($this->prompt);
        } catch (\Exception $e) {
            //エラー処理
            session()->flash('error', $e->getMessage());
        }

        //inputをクリア
        $this->prompt = '';

        return redirect()->to('/events');
    }
}
