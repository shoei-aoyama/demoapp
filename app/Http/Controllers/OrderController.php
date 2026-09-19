<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function create(): View
    {
        $items = $this->cartItems();

        return view('orders.confirm', ['items' => $items]);
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $items = $this->cartItems();

        if ($items->isEmpty()) {
            return redirect()->route('orders.create')->with('error', 'カートが空です。');
        }

        $stoppedItems = $items->filter(fn (array $item) => $item['product']->is_stopped);

        if ($stoppedItems->isNotEmpty()) {
            $names = $stoppedItems->pluck('product.name')->implode('、');

            return redirect()->route('orders.create')
                ->with('error', "販売を終了した商品が含まれているため注文できません: {$names}。カートから削除してください。");
        }

        $totalPrice = $items->sum(fn (array $item) => $item['product']->price * $item['quantity']);

        $order = DB::transaction(function () use ($request, $items, $totalPrice) {
            $order = Order::create([
                'customer_name' => $request->validated('customer_name'),
                'customer_email' => $request->validated('customer_email'),
                'total_price' => $totalPrice,
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'product_id' => $item['product']->id,
                    'price' => $item['product']->price,
                    'quantity' => $item['quantity'],
                ]);
            }

            return $order;
        });

        session(['cart' => []]);
        session()->push('order_ids', $order->id);

        return redirect()->route('orders.complete', $order);
    }

    public function complete(Order $order): View
    {
        abort_unless(in_array($order->id, session('order_ids', [])), 404);

        $order->load('items.product');

        return view('orders.complete', compact('order'));
    }

    public function index(): View
    {
        $orders = Order::whereIn('id', session('order_ids', []))
            ->with('items.product')
            ->latest()
            ->get();

        return view('orders.index', compact('orders'));
    }

    /**
     * @return Collection<int, array{product: Product, quantity: int}>
     */
    private function cartItems(): Collection
    {
        $cart = session('cart', []);

        return collect($cart)->map(function (int $quantity, string $productId) {
            $product = Product::find($productId);

            return $product ? ['product' => $product, 'quantity' => $quantity] : null;
        })->filter()->values();
    }
}
