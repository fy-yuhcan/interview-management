<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interview Management</title>
    <!-- Livewire 用のスタイルシート -->
    @livewireStyles
</head>
<body>
    @livewire('header')
    
    <div class="container">
        @yield('content')
        @livewire('calendar')
    </div>

    <!-- Livewire 用のスクリプト -->
    @livewireScripts
</body>
</html>
