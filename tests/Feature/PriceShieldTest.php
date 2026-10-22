<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PriceShieldTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CategorySeeder::class);
        $this->seed(ProductSeeder::class);

        Route::middleware('approved')->get('/test-approved-feature', function () {
            return response('Approved Area Accessed', 200);
        });
    }

    private function createCustomer(string $status = 'approved', array $attributes = []): User
    {
        $user = new User;
        $user->name = $attributes['name'] ?? 'Sample Business';
        $user->email = $attributes['email'] ?? 'business@wholesale.test';
        $user->company_name = $attributes['company_name'] ?? 'Wholesale Mobile Client LLC';
        $user->phone = $attributes['phone'] ?? '+1-555-0123';
        $user->password = Hash::make($attributes['password'] ?? 'secret1234');
        $user->status = $status;
        $user->is_admin = $attributes['is_admin'] ?? false;
        $user->save();

        return $user;
    }

    public function test_guest_sees_no_seeded_prices_or_currency_on_catalog_and_product_page(): void
    {
        // 1. Catalog page
        $catalogResponse = $this->get(route('catalog.index'));
        $catalogResponse->assertOk();
        $catalogHtml = $catalogResponse->getContent();

        $this->assertStringNotContainsString('$', $catalogHtml);
        $this->assertStringNotContainsString('USD', $catalogHtml);
        $this->assertStringNotContainsString('€', $catalogHtml);
        $catalogResponse->assertSeeText('Log in with an approved account to see wholesale prices.');
        $catalogResponse->assertSee(route('login'));

        // 2. Product detail page
        $product = Product::where('slug', 'apex-pro-5g-256gb-titanium')->firstOrFail();
        $seededPrice = number_format((float) $product->price, 2);

        $detailResponse = $this->get(route('catalog.show', $product->slug));
        $detailResponse->assertOk();
        $detailHtml = $detailResponse->getContent();

        $this->assertStringNotContainsString('$', $detailHtml);
        $this->assertStringNotContainsString('USD', $detailHtml);
        $this->assertStringNotContainsString($seededPrice, $detailHtml);
        $detailResponse->assertSeeText('Log in with an approved account to see wholesale prices.');
        $detailResponse->assertSee(route('login'));
    }

    public function test_pending_user_sees_no_prices_on_catalog_or_product_page(): void
    {
        $pendingUser = $this->createCustomer('pending', ['email' => 'pending-client@wholesale.test']);

        // Catalog as pending user
        $catalogResponse = $this->actingAs($pendingUser)->get(route('catalog.index'));
        $catalogResponse->assertOk();
        $catalogHtml = $catalogResponse->getContent();

        $this->assertStringNotContainsString('$', $catalogHtml);
        $this->assertStringNotContainsString('USD', $catalogHtml);
        $catalogResponse->assertSee('Log in with an approved account to see wholesale prices.');

        // Product detail as pending user
        $product = Product::where('slug', 'apex-pro-5g-256gb-titanium')->firstOrFail();
        $seededPrice = number_format((float) $product->price, 2);

        $detailResponse = $this->actingAs($pendingUser)->get(route('catalog.show', $product->slug));
        $detailResponse->assertOk();
        $detailHtml = $detailResponse->getContent();

        $this->assertStringNotContainsString('$', $detailHtml);
        $this->assertStringNotContainsString('USD', $detailHtml);
        $this->assertStringNotContainsString($seededPrice, $detailHtml);
        $detailResponse->assertSee('Log in with an approved account to see wholesale prices.');
    }

    public function test_rejected_user_sees_no_prices_on_catalog_or_product_page(): void
    {
        $rejectedUser = $this->createCustomer('rejected', ['email' => 'rejected-client@wholesale.test']);

        // Catalog as rejected user
        $catalogResponse = $this->actingAs($rejectedUser)->get(route('catalog.index'));
        $catalogResponse->assertOk();
        $catalogHtml = $catalogResponse->getContent();

        $this->assertStringNotContainsString('$', $catalogHtml);
        $this->assertStringNotContainsString('USD', $catalogHtml);
        $catalogResponse->assertSee('Log in with an approved account to see wholesale prices.');

        // Product detail as rejected user
        $product = Product::where('slug', 'apex-pro-5g-256gb-titanium')->firstOrFail();
        $seededPrice = number_format((float) $product->price, 2);

        $detailResponse = $this->actingAs($rejectedUser)->get(route('catalog.show', $product->slug));
        $detailResponse->assertOk();
        $detailHtml = $detailResponse->getContent();

        $this->assertStringNotContainsString('$', $detailHtml);
        $this->assertStringNotContainsString('USD', $detailHtml);
        $this->assertStringNotContainsString($seededPrice, $detailHtml);
        $detailResponse->assertSee('Log in with an approved account to see wholesale prices.');
    }

    public function test_approved_customer_sees_prices_on_both_pages(): void
    {
        $approvedUser = $this->createCustomer('approved', ['email' => 'approved-client@wholesale.test']);

        // Catalog as approved customer
        $catalogResponse = $this->actingAs($approvedUser)->get(route('catalog.index'));
        $catalogResponse->assertOk();
        $catalogHtml = $catalogResponse->getContent();

        $product = Product::where('slug', 'apex-pro-5g-256gb-titanium')->firstOrFail();
        $this->assertStringContainsString($product->formattedPrice(), $catalogHtml);
        $this->assertStringContainsString('$', $catalogHtml);

        // Product detail as approved customer
        $detailResponse = $this->actingAs($approvedUser)->get(route('catalog.show', $product->slug));
        $detailResponse->assertOk();
        $detailHtml = $detailResponse->getContent();

        $this->assertStringContainsString($product->formattedPrice(), $detailHtml);
        $detailResponse->assertSee('Approved Wholesale Pricing');
    }

    public function test_admin_user_sees_prices_on_both_pages(): void
    {
        $admin = $this->createCustomer('approved', [
            'name' => 'System Admin',
            'email' => 'admin@wholesale.test',
            'is_admin' => true,
        ]);

        $catalogResponse = $this->actingAs($admin)->get(route('catalog.index'));
        $catalogResponse->assertOk();

        $product = Product::where('slug', 'apex-pro-5g-256gb-titanium')->firstOrFail();
        $this->assertStringContainsString($product->formattedPrice(), $catalogResponse->getContent());

        $detailResponse = $this->actingAs($admin)->get(route('catalog.show', $product->slug));
        $detailResponse->assertOk();
        $this->assertStringContainsString($product->formattedPrice(), $detailResponse->getContent());
    }

    public function test_price_column_is_not_selected_for_non_approved_users(): void
    {
        // 1. Guest
        $guestCatalog = $this->get(route('catalog.index'));
        $guestProducts = $guestCatalog->viewData('products');
        foreach ($guestProducts as $prod) {
            $this->assertArrayNotHasKey('price', $prod->getAttributes(), 'Price column must not be selected for guests');
        }

        $guestDetail = $this->get(route('catalog.show', 'apex-pro-5g-256gb-titanium'));
        $this->assertArrayNotHasKey('price', $guestDetail->viewData('product')->getAttributes());

        // 2. Pending user
        $pendingUser = $this->createCustomer('pending', ['email' => 'pending-attr@wholesale.test']);
        $pendingCatalog = $this->actingAs($pendingUser)->get(route('catalog.index'));
        foreach ($pendingCatalog->viewData('products') as $prod) {
            $this->assertArrayNotHasKey('price', $prod->getAttributes(), 'Price column must not be selected for pending users');
        }

        // 3. Rejected user
        $rejectedUser = $this->createCustomer('rejected', ['email' => 'rejected-attr@wholesale.test']);
        $rejectedCatalog = $this->actingAs($rejectedUser)->get(route('catalog.index'));
        foreach ($rejectedCatalog->viewData('products') as $prod) {
            $this->assertArrayNotHasKey('price', $prod->getAttributes(), 'Price column must not be selected for rejected users');
        }

        // 4. Approved user (verify price IS selected)
        $approvedUser = $this->createCustomer('approved', ['email' => 'approved-attr@wholesale.test']);
        $approvedCatalog = $this->actingAs($approvedUser)->get(route('catalog.index'));
        foreach ($approvedCatalog->viewData('products') as $prod) {
            $this->assertArrayHasKey('price', $prod->getAttributes(), 'Price column must be selected for approved users');
        }
    }

    public function test_approving_a_pending_user_makes_prices_visible_on_next_request(): void
    {
        $customer = $this->createCustomer('pending', ['email' => 'transition-app@wholesale.test']);

        // First request while pending -> no prices
        $firstResponse = $this->actingAs($customer)->get(route('catalog.index'));
        $firstResponse->assertOk();
        $this->assertStringNotContainsString('$', $firstResponse->getContent());

        // Status changed to approved in database
        $customer->status = 'approved';
        $customer->save();

        // Next request -> prices immediately visible
        $secondResponse = $this->actingAs($customer)->get(route('catalog.index'));
        $secondResponse->assertOk();
        $this->assertStringContainsString('$', $secondResponse->getContent());

        $firstProduct = $secondResponse->viewData('products')->first();
        $this->assertArrayHasKey('price', $firstProduct->getAttributes());
    }

    public function test_rejecting_an_approved_user_hides_prices_on_next_request(): void
    {
        $customer = $this->createCustomer('approved', ['email' => 'transition-rej@wholesale.test']);

        // First request while approved -> prices visible
        $firstResponse = $this->actingAs($customer)->get(route('catalog.index'));
        $firstResponse->assertOk();
        $this->assertStringContainsString('$', $firstResponse->getContent());

        // Status changed to rejected in database
        $customer->status = 'rejected';
        $customer->save();

        // Next request -> prices immediately hidden
        $secondResponse = $this->actingAs($customer)->get(route('catalog.index'));
        $secondResponse->assertOk();
        $this->assertStringNotContainsString('$', $secondResponse->getContent());

        $firstProduct = $secondResponse->viewData('products')->first();
        $this->assertArrayNotHasKey('price', $firstProduct->getAttributes());
    }

    public function test_approved_middleware_allows_approved_customers_and_admins(): void
    {
        $customer = $this->createCustomer('approved', ['email' => 'gate-approved@wholesale.test']);
        $response = $this->actingAs($customer)->get('/test-approved-feature');
        $response->assertOk();
        $response->assertSee('Approved Area Accessed');

        $admin = $this->createCustomer('approved', [
            'email' => 'gate-admin@wholesale.test',
            'is_admin' => true,
        ]);
        $adminResponse = $this->actingAs($admin)->get('/test-approved-feature');
        $adminResponse->assertOk();
        $adminResponse->assertSee('Approved Area Accessed');
    }

    public function test_approved_middleware_redirects_guest_and_forbids_unapproved_users(): void
    {
        // 1. Guest -> redirected to login
        $guestResponse = $this->get('/test-approved-feature');
        $guestResponse->assertRedirect(route('login'));

        // 2. Pending user -> 403 Forbidden
        $pendingUser = $this->createCustomer('pending', ['email' => 'gate-pending@wholesale.test']);
        $pendingResponse = $this->actingAs($pendingUser)->get('/test-approved-feature');
        $pendingResponse->assertForbidden();

        // 3. Rejected user -> 403 Forbidden
        $rejectedUser = $this->createCustomer('rejected', ['email' => 'gate-rejected@wholesale.test']);
        $rejectedResponse = $this->actingAs($rejectedUser)->get('/test-approved-feature');
        $rejectedResponse->assertForbidden();
    }
}
