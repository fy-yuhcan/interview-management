<?php

namespace App\Livewire;

use Livewire\Component;

class OauthLogin extends Component
{
    public function render()
    {
        return view('livewire.oauth-login');
    }

    public function loginGoogle()
    {
        return redirect()->route('redirect', ['provider' => 'google']);
    }
}
