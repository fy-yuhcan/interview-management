<div>
    <form wire:submit.prevent="submit">
        <textarea wire:model="prompt" placeholder="イベントの内容を入力してください"></textarea>
        <button type="submit">イベント作成(自動デプロイ確認用)</button>
    </form>

    @if (session()->has('message'))
    <div style="color: green;">{{ session('message') }}</div>
    @endif

    @if (session()->has('error'))
    <div style="color: red;">{{ session('error') }}</div>
    @endif
</div>
