<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'ハンドドリップコーヒーセット',
                'description' => '自宅で本格的なハンドドリップコーヒーが楽しめるスターターセットです。ドリッパー・サーバー・専用フィルターが付属します。',
                'image' => 'https://picsum.photos/seed/product-1/640/480',
                'price' => 4980,
                'is_stopped' => false,
            ],
            [
                'name' => '木製カッティングボード',
                'description' => '天然木を使用した一枚板のカッティングボードです。食材のカットから、そのまま食卓での盛り付けまで使えます。',
                'image' => 'https://picsum.photos/seed/product-2/640/480',
                'price' => 3200,
                'is_stopped' => false,
            ],
            [
                'name' => 'アロマディフューザー',
                'description' => '超音波式のアロマディフューザーです。7色のライトで空間の雰囲気づくりにも役立ちます。',
                'image' => 'https://picsum.photos/seed/product-3/640/480',
                'price' => 5980,
                'is_stopped' => false,
            ],
            [
                'name' => 'ステンレスタンブラー',
                'description' => '保温・保冷に優れたステンレス製タンブラーです。350mlサイズで持ち運びやすい形状です。',
                'image' => 'https://picsum.photos/seed/product-4/640/480',
                'price' => 2400,
                'is_stopped' => false,
            ],
            [
                'name' => 'リネンエプロン',
                'description' => '肌触りの良いリネン素材を使用したエプロンです。キッチンだけでなく、ガーデニング等の作業にも使えます。',
                'image' => 'https://picsum.photos/seed/product-5/640/480',
                'price' => 3800,
                'is_stopped' => false,
            ],
            [
                'name' => '季節限定ブレンド紅茶（販売終了）',
                'description' => '冬季限定で販売していたスパイスブレンド紅茶です。現在は販売を終了しています。',
                'image' => 'https://picsum.photos/seed/product-6/640/480',
                'price' => 1800,
                'is_stopped' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
