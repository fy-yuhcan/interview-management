<div>
    <h1>ユーザー設定</h1>

    @if (session()->has('message'))
    <div style="color: green;">
        {{ session('message') }}
    </div>
    @endif

    <form wire:submit.prevent="updateEmail">
        <div>
            <label>メールアドレス:</label>
            <input type="email" wire:model="email" placeholder="{{ $user->email }}">
        </div>

        <button type="submit">更新</button>
    </form>
</div>
