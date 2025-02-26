<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserSetting extends Component
{
    public $email;

    public function mount()
    {
        $this->email = Auth::user()->email;
    }


    /**
     * メールアドレスを更新する
     */
    public function updateEmail()
    {
        /**
         * @var \App\Models\User $user
         */
        $user = Auth::user();

        $user->update([
            'email' => $this->email,
        ]);
    }

    public function render()
    {
        return view('livewire.user-setting');
    }
}
