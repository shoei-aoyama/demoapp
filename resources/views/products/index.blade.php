@extends('layouts.app')

@section('title', '商品一覧 - 商品注文デモ')

@section('content')
    <h1 class="mb-8 text-2xl font-bold text-gray-900">商品一覧</h1>

    @if ($products->isEmpty())
        <p class="rounded-lg border border-gray-200 bg-white p-8 text-center text-gray-500">
            現在販売中の商品はありません
        </p>
    @else
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($products as $product)
                <a
                    href="{{ route('products.show', $product) }}"
                    class="group overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition hover:shadow-md"
                >
                    <img
                        src="{{ $product->image }}"
                        alt="{{ $product->name }}"
                        class="h-48 w-full object-cover"
                    >
                    <div class="p-4">
                        <h2 class="font-semibold text-gray-900 group-hover:text-gray-700">
                            {{ $product->name }}
                        </h2>
                        <p class="mt-1 line-clamp-2 text-sm text-gray-500">
                            {{ $product->description }}
                        </p>
                        <p class="mt-3 text-lg font-bold text-gray-900">
                            ¥{{ number_format($product->price) }}
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection
