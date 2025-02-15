<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Header extends Component
{
    public $name;

    public function render()
    {
        return view('livewire.header');
    }

    public function mount()
    {
        $this->name = Auth::check() ? Auth::user()->name : '';
    }

    public function transferUserSetting()
    {
        return redirect()->route('user.setting');
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }
}
