@extends('layouts.app')

@section('title', '注文内容の確認 - 商品注文デモ')

@section('content')
    <h1 class="mb-8 text-2xl font-bold text-gray-900">注文内容の確認</h1>

    @if (session('error'))
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if ($items->isEmpty())
        <div class="mb-6 rounded-lg border border-gray-200 bg-white p-6 text-center text-gray-500">
            カートが空です。商品をカートに追加してから注文手続きを行ってください。
        </div>
    @else
        <div class="mb-8 space-y-3">
            @foreach ($items as $item)
                <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-white p-4">
                    <div>
                        <p class="font-medium text-gray-900">{{ $item['product']->name }}</p>
                        <p class="text-sm text-gray-500">数量: {{ $item['quantity'] }}</p>
                    </div>
                    <p class="text-gray-900">¥{{ number_format($item['product']->price) }}</p>
                </div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('orders.store') }}" class="max-w-md space-y-5">
        @csrf

        <div>
            <label for="customer_name" class="block text-sm font-medium text-gray-700">氏名</label>
            <input
                type="text"
                id="customer_name"
                name="customer_name"
                value="{{ old('customer_name') }}"
                {{ $items->isEmpty() ? 'disabled' : '' }}
                class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 disabled:bg-gray-100"
            >
            @error('customer_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="customer_email" class="block text-sm font-medium text-gray-700">メールアドレス</label>
            <input
                type="email"
                id="customer_email"
                name="customer_email"
                value="{{ old('customer_email') }}"
                {{ $items->isEmpty() ? 'disabled' : '' }}
                class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 disabled:bg-gray-100"
            >
            @error('customer_email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            {{ $items->isEmpty() ? 'disabled' : '' }}
            class="w-full rounded-lg bg-gray-900 px-6 py-3 font-semibold text-white transition hover:bg-gray-700 disabled:cursor-not-allowed disabled:bg-gray-300"
        >
            注文を確定する
        </button>
    </form>
@endsection
