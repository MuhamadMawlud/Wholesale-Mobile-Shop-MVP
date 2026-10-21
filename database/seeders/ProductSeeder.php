<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $flagship = Category::where('slug', 'flagship-smartphones')->first();
        $budget = Category::where('slug', 'budget-mid-range-phones')->first();
        $power = Category::where('slug', 'charging-power')->first();
        $protection = Category::where('slug', 'protection-cases')->first();
        $audio = Category::where('slug', 'audio-wearables')->first();

        $products = [
            [
                'category_id' => $flagship->id,
                'name' => 'Apex Pro 5G 256GB Titanium',
                'slug' => 'apex-pro-5g-256gb-titanium',
                'description' => 'Flagship wholesale unit with aerospace titanium frame, 120Hz LTPO display, and 200MP camera system. Factory unlocked master carton.',
                'image' => null,
                'price' => 799.00,
                'stock' => 45,
            ],
            [
                'category_id' => $flagship->id,
                'name' => 'Nova Ultra 512GB Phantom Black',
                'slug' => 'nova-ultra-512gb-phantom-black',
                'description' => 'Top-tier enterprise device featuring high-density battery, stylus support, and quad-lens imaging. Export grade packaging.',
                'image' => null,
                'price' => 899.00,
                'stock' => 25,
            ],
            [
                'category_id' => $flagship->id,
                'name' => 'Vortex Fold Pro 512GB Mystic Bronze',
                'slug' => 'vortex-fold-pro-512gb-mystic-bronze',
                'description' => 'Premium foldable handset with ultra-thin flexible glass and dual screens. Sealed wholesale master boxes.',
                'image' => null,
                'price' => 1250.00,
                'stock' => 12,
            ],
            [
                'category_id' => $budget->id,
                'name' => 'Echo Lite 128GB Midnight Blue',
                'slug' => 'echo-lite-128gb-midnight-blue',
                'description' => 'High-demand entry smartphone with 5000mAh battery and dual SIM capability. Ideal for wholesale retail distribution.',
                'image' => null,
                'price' => 185.00,
                'stock' => 120,
            ],
            [
                'category_id' => $budget->id,
                'name' => 'Pulse 5G 128GB Frost Silver',
                'slug' => 'pulse-5g-128gb-frost-silver',
                'description' => 'Affordable 5G handset with 90Hz smooth screen and rapid 33W charging. Packed 10 units per master case.',
                'image' => null,
                'price' => 245.00,
                'stock' => 75,
            ],
            [
                'category_id' => $budget->id,
                'name' => 'Zenith Core 64GB Slate Gray',
                'slug' => 'zenith-core-64gb-slate-gray',
                'description' => 'Basic fleet phone for logistics and corporate deployments. Compact and durable construction.',
                'image' => null,
                'price' => 110.00,
                'stock' => 0, // Out of stock for testing
            ],
            [
                'category_id' => $power->id,
                'name' => '65W GaN Dual-Port Fast Charger (Bulk 10-Pack)',
                'slug' => '65w-gan-fast-charger-10-pack',
                'description' => 'Compact Gallium Nitride wall adapters with USB-C and USB-A outputs. Bulk packed in distributor cartons of 10 units.',
                'image' => null,
                'price' => 140.00,
                'stock' => 60,
            ],
            [
                'category_id' => $power->id,
                'name' => 'Braided USB-C to USB-C Cable 2M (Pack of 20)',
                'slug' => 'braided-usbc-cable-2m-20-pack',
                'description' => '100W PD compatible braided reinforced cables. Packaged in wholesale polybags of 20 pieces.',
                'image' => null,
                'price' => 55.00,
                'stock' => 150,
            ],
            [
                'category_id' => $power->id,
                'name' => '20000mAh Magnetic Wireless Power Bank (5-Pack)',
                'slug' => '20000mah-magnetic-powerbank-5-pack',
                'description' => 'Magnetic wireless external batteries with 15W Qi fast charging. Pack of 5 retail-boxed units.',
                'image' => null,
                'price' => 165.00,
                'stock' => 40,
            ],
            [
                'category_id' => $protection->id,
                'name' => 'Shockproof Armor Case Bundle for Apex Pro (50 pcs)',
                'slug' => 'shockproof-armor-case-apex-pro-50-pack',
                'description' => 'Military drop-tested clear hybrid armor cases with reinforced corners. Master carton of 50 individual polybags.',
                'image' => null,
                'price' => 95.00,
                'stock' => 80,
            ],
            [
                'category_id' => $protection->id,
                'name' => '9H Tempered Glass Screen Protector Lot (100 pcs)',
                'slug' => '9h-tempered-glass-lot-100-pack',
                'description' => 'High-transparency oleophobic coated screen protectors with alignment trays. Workshop bundle of 100 units.',
                'image' => null,
                'price' => 70.00,
                'stock' => 200,
            ],
            [
                'category_id' => $audio->id,
                'name' => 'Active Noise Cancelling TWS Earbuds (Bulk 10-Pack)',
                'slug' => 'anc-tws-earbuds-10-pack',
                'description' => 'Bluetooth 5.3 true wireless stereo earbuds with hybrid ANC and wireless charging case. Carton of 10 boxes.',
                'image' => null,
                'price' => 220.00,
                'stock' => 35,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(['slug' => $product['slug']], $product);
        }
    }
}
