@extends('layouts.app')

@section('title', '注文完了 - 商品注文デモ')

@section('content')
    <div class="mx-auto max-w-md text-center">
        <h1 class="text-2xl font-bold text-gray-900">ご注文ありがとうございました</h1>
        <p class="mt-2 text-gray-600">ご注文を承りました。</p>

        <div class="mt-8 space-y-3 rounded-xl bg-white p-6 text-left shadow-sm">
            @foreach ($order->items as $item)
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium text-gray-900">{{ $item->product->name }}</p>
                        <p class="text-sm text-gray-500">数量: {{ $item->quantity }}</p>
                    </div>
                    <p class="font-bold text-gray-900">¥{{ number_format($item->price) }}</p>
                </div>
            @endforeach
        </div>

        <a
            href="{{ route('orders.index') }}"
            class="mt-8 inline-block rounded-lg bg-red-600 px-6 py-3 font-semibold text-white transition hover:bg-red-700"
        >
            注文履歴を見る
        </a>
    </div>
@endsection
