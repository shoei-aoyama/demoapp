@extends('layouts.app')

@section('title', $product->name . ' - 商品注文デモ')

@section('content')
    <a href="{{ route('products.index') }}" class="mb-6 inline-block text-sm text-gray-500 hover:text-gray-700">
        ← 商品一覧に戻る
    </a>

    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
        <img
            src="{{ $product->image }}"
            alt="{{ $product->name }}"
            class="aspect-square w-full rounded-xl object-cover shadow-sm"
        >

        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
            <p class="mt-4 text-2xl font-bold text-gray-900">¥{{ number_format($product->price) }}</p>
            <p class="mt-4 leading-relaxed text-gray-600">{{ $product->description }}</p>

            <form method="POST" action="{{ route('cart.store', $product) }}" class="mt-8">
                @csrf
                <button
                    type="submit"
                    class="w-full rounded-lg bg-red-600 px-6 py-3 font-semibold text-white transition hover:bg-red-700"
                >
                    カートに追加
                </button>
            </form>
        </div>
    </div>
@endsection
