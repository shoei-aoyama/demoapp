<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', '商品注文デモ')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">
    <header class="border-b border-gray-200 bg-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between px-6 py-4">
            <a href="{{ route('products.index') }}" class="text-lg font-semibold tracking-tight text-gray-900">
                商品注文デモ
            </a>
            <nav class="flex items-center gap-6 text-sm font-medium text-gray-600">
                <a href="{{ route('products.index') }}" class="hover:text-gray-900">商品一覧</a>
                <a href="{{ route('cart.show') }}" class="hover:text-gray-900">カート</a>
                <a href="{{ route('orders.index') }}" class="hover:text-gray-900">注文履歴</a>
            </nav>
        </div>
    </header>

    <main class="mx-auto max-w-5xl px-6 py-10">
        @yield('content')
    </main>
</body>
</html>
