@extends('layouts.app')

@section('title', 'カート - 商品注文デモ')

@section('content')
    <h1 class="mb-8 text-2xl font-bold text-gray-900">カート</h1>

    @if (session('error'))
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if ($items->isEmpty())
        <p class="rounded-lg border border-gray-200 bg-white p-8 text-center text-gray-500">
            カートに商品がありません。
            <a href="{{ route('products.index') }}" class="text-gray-900 underline">商品一覧を見る</a>
        </p>
    @else
        <div class="space-y-4">
            @foreach ($items as $item)
                @php $product = $item['product']; @endphp
                <div class="flex items-center gap-4 rounded-xl border border-gray-200 bg-white p-4">
                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="h-20 w-20 rounded-lg object-cover">

                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <h2 class="font-semibold text-gray-900">{{ $product->name }}</h2>
                            @if ($product->is_stopped)
                                <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-700">
                                    販売終了
                                </span>
                            @endif
                        </div>
                        <p class="mt-1 text-sm text-gray-500">¥{{ number_format($product->price) }}</p>
                    </div>

                    <form method="POST" action="{{ route('cart.update', $product) }}" class="flex items-center gap-2">
                        @csrf
                        @method('PATCH')
                        <input
                            type="number"
                            name="quantity"
                            value="{{ $item['quantity'] }}"
                            min="1"
                            max="99"
                            {{ $product->is_stopped ? 'disabled' : '' }}
                            class="w-20 rounded-lg border border-gray-300 px-3 py-2 text-sm disabled:bg-gray-100 disabled:text-gray-400"
                        >
                        @unless ($product->is_stopped)
                            <button type="submit" class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                                変更
                            </button>
                        @endunless
                    </form>

                    <form method="POST" action="{{ route('cart.destroy', $product) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-700">
                            削除
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="mt-8 flex items-center justify-between">
            <form method="POST" action="{{ route('cart.clear') }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                    カートを空にする
                </button>
            </form>

            <a
                href="{{ route('orders.create') }}"
                class="rounded-lg bg-gray-900 px-6 py-3 font-semibold text-white transition hover:bg-gray-700"
            >
                注文手続きへ進む
            </a>
        </div>
    @endif
@endsection
