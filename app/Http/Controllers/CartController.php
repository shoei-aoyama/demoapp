<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function show(): View
    {
        $cart = session('cart', []);

        $items = collect($cart)->map(function (int $quantity, string $productId) {
            $product = Product::find($productId);

            return $product ? ['product' => $product, 'quantity' => $quantity] : null;
        })->filter()->values();

        return view('cart.show', ['items' => $items]);
    }

    public function store(Product $product): RedirectResponse
    {
        if ($product->is_stopped) {
            return back()->with('error', 'この商品は現在販売を終了しています。');
        }

        $cart = session('cart', []);
        $key = (string) $product->id;
        $cart[$key] = min(($cart[$key] ?? 0) + 1, 99);

        session(['cart' => $cart]);

        return redirect()->route('cart.show');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        if ($product->is_stopped) {
            return back()->with('error', 'この商品は現在販売を終了しているため、数量を変更できません。');
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ], [], ['quantity' => '数量']);

        $cart = session('cart', []);
        $key = (string) $product->id;

        if (! array_key_exists($key, $cart)) {
            return back()->with('error', 'カートに存在しない商品です。');
        }

        $cart[$key] = $validated['quantity'];
        session(['cart' => $cart]);

        return back();
    }

    public function destroy(Product $product): RedirectResponse
    {
        $cart = session('cart', []);
        unset($cart[(string) $product->id]);
        session(['cart' => $cart]);

        return back();
    }

    public function clear(): RedirectResponse
    {
        session(['cart' => []]);

        return back();
    }
}
