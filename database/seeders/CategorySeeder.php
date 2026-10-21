<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Flagship Smartphones',
                'slug' => 'flagship-smartphones',
            ],
            [
                'name' => 'Budget & Mid-Range Phones',
                'slug' => 'budget-mid-range-phones',
            ],
            [
                'name' => 'Charging & Power',
                'slug' => 'charging-power',
            ],
            [
                'name' => 'Protection & Cases',
                'slug' => 'protection-cases',
            ],
            [
                'name' => 'Audio & Wearables',
                'slug' => 'audio-wearables',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
