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
        <div class="mb-6 rounded-lg bg-white p-6 text-center text-gray-500 shadow-sm">
            カートが空です。商品をカートに追加してから注文手続きを行ってください。
        </div>
    @else
        <div class="mb-8 space-y-3">
            @foreach ($items as $item)
                <div class="flex items-center justify-between rounded-lg bg-white p-4 shadow-sm">
                    <div>
                        <p class="font-medium text-gray-900">{{ $item['product']->name }}</p>
                        <p class="text-sm text-gray-500">数量: {{ $item['quantity'] }}</p>
                    </div>
                    <p class="font-bold text-gray-900">¥{{ number_format($item['product']->price) }}</p>
                </div>
            @endforeach
        </div>

        <div class="mb-8 rounded-lg bg-white p-4 text-sm text-gray-600 shadow-sm">
            <p>ご注文者: <span class="font-medium text-gray-900">{{ session('customer_name') }}</span>（{{ session('customer_email') }}）</p>
        </div>
    @endif

    <form method="POST" action="{{ route('orders.store') }}">
        @csrf
        <button
            type="submit"
            {{ $items->isEmpty() ? 'disabled' : '' }}
            class="w-full max-w-md rounded-lg bg-red-600 px-6 py-3 font-semibold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:bg-gray-300"
        >
            注文を確定する
        </button>
    </form>
@endsection
