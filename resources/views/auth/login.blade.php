@extends('layouts.app')

@section('title', 'ログイン - 商品注文デモ')

@section('content')
    <div class="mx-auto max-w-sm">
        <h1 class="mb-8 text-2xl font-bold text-gray-900">ログイン</h1>

        <p class="mb-6 text-sm text-gray-500">
            注文手続きにはログインが必要です。テストアカウントでログインしてください。
        </p>

        <form method="POST" action="{{ route('login.attempt') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">メールアドレス</label>
                <input
                    type="text"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                >
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">パスワード</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2"
                >
            </div>

            <button
                type="submit"
                class="w-full rounded-lg bg-red-600 px-6 py-3 font-semibold text-white transition hover:bg-red-700"
            >
                ログイン
            </button>
        </form>
    </div>
@endsection
