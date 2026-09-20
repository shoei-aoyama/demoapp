@extends('layouts.app')

@section('title', '注文履歴 - 商品注文デモ')

@section('content')
    <h1 class="mb-8 text-2xl font-bold text-gray-900">注文履歴</h1>

    @if ($orders->isEmpty())
        <p class="rounded-lg bg-white p-8 text-center text-gray-500 shadow-sm">
            このセッションではまだ注文がありません。
        </p>
    @else
        <div class="space-y-6">
            @foreach ($orders as $order)
                <div class="rounded-xl bg-white p-6 shadow-sm">
                    <p class="mb-4 text-sm text-gray-500">
                        注文日時: {{ $order->created_at->format('Y/m/d H:i') }}
                    </p>
                    <div class="space-y-2">
                        @foreach ($order->items as $item)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-900">{{ $item->product->name }}（数量: {{ $item->quantity }}）</span>
                                <span class="text-gray-600">¥{{ number_format($item->price) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
