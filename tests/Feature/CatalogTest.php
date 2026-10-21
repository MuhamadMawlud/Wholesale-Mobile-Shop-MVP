<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
        $this->seed(ProductSeeder::class);
    }

    public function test_catalog_page_loads_successfully_and_displays_products(): void
    {
        $response = $this->get(route('catalog.index'));

        $response->assertOk();
        $response->assertSee('Apex Pro 5G 256GB Titanium');
        $response->assertSee('Flagship Smartphones');
        $response->assertSee('In Stock (45 available)');
        $response->assertSee('Out of Stock');
        $response->assertSee('Wholesale Price: Available to approved customers');
    }

    public function test_catalog_filters_by_category(): void
    {
        $response = $this->get(route('catalog.index', ['category' => 'charging-power']));

        $response->assertOk();
        $response->assertSee('65W GaN Dual-Port Fast Charger');
        $response->assertDontSee('Apex Pro 5G 256GB Titanium');
    }

    public function test_product_detail_page_loads_successfully(): void
    {
        $product = Product::where('slug', 'apex-pro-5g-256gb-titanium')->firstOrFail();

        $response = $this->get(route('catalog.show', $product->slug));

        $response->assertOk();
        $response->assertSee($product->name);
        $response->assertSee($product->description);
        $response->assertSee('In Stock (45 available)');
        $response->assertSee('Wholesale Pricing Shielded');
    }

    public function test_non_existent_product_returns_404(): void
    {
        $response = $this->get('/products/non-existent-product-slug');

        $response->assertNotFound();
    }

    public function test_catalog_query_does_not_select_price_column_for_guests(): void
    {
        $response = $this->get(route('catalog.index'));

        $response->assertOk();
        $products = $response->viewData('products');
        $this->assertNotEmpty($products);

        foreach ($products as $product) {
            $this->assertArrayNotHasKey(
                'price',
                $product->getAttributes(),
                'The price column must not be selected from the database for guest visitors.'
            );
        }
    }

    public function test_product_detail_query_does_not_select_price_column_for_guests(): void
    {
        $response = $this->get(route('catalog.show', 'apex-pro-5g-256gb-titanium'));

        $response->assertOk();
        $product = $response->viewData('product');

        $this->assertArrayNotHasKey(
            'price',
            $product->getAttributes(),
            'The price column must not be selected from the database for guest visitors.'
        );
    }

    public function test_seeded_wholesale_prices_and_currency_symbols_never_appear_in_catalog_html_for_guests(): void
    {
        $response = $this->get(route('catalog.index'));
        $response->assertOk();
        $content = $response->getContent();

        // Verify currency symbols are strictly absent
        $this->assertStringNotContainsString('$', $content, 'Currency symbol $ must not appear in guest catalog.');
        $this->assertStringNotContainsString('USD', $content, 'Currency code USD must not appear in guest catalog.');
        $this->assertStringNotContainsString('€', $content, 'Currency symbol € must not appear in guest catalog.');

        // Verify all seeded price figures are absent from HTML
        $seededPrices = Product::pluck('price')->toArray();
        $this->assertNotEmpty($seededPrices);

        foreach ($seededPrices as $price) {
            $formattedPrice = number_format((float) $price, 2);
            $this->assertStringNotContainsString(
                $formattedPrice,
                $content,
                "Seeded price {$formattedPrice} must not be present in the HTML."
            );
        }
    }

    public function test_seeded_wholesale_price_does_not_appear_on_product_detail_page_for_guests(): void
    {
        $product = Product::where('slug', 'apex-pro-5g-256gb-titanium')->firstOrFail();
        $seededPrice = number_format((float) $product->price, 2);

        $response = $this->get(route('catalog.show', $product->slug));
        $response->assertOk();
        $content = $response->getContent();

        $this->assertStringNotContainsString('$', $content);
        $this->assertStringNotContainsString('USD', $content);
        $this->assertStringNotContainsString(
            $seededPrice,
            $content,
            "Seeded wholesale price {$seededPrice} must not appear on guest product detail page."
        );
    }
}
