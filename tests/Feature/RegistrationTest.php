<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
        $response->assertSee('Wholesale Customer Registration');
        $response->assertSee('Contact Name');
        $response->assertSee('Business Email Address');
        $response->assertSee('Company / Business Name');
        $response->assertSee('Contact Phone Number');
    }

    public function test_registration_succeeds_and_creates_pending_user(): void
    {
        $payload = [
            'name' => 'Alice Johnson',
            'email' => 'alice@wholesalebiz.test',
            'company_name' => 'Wholesale Tech Distributors LLC',
            'phone' => '+1-555-0145',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ];

        $response = $this->post(route('register'), $payload);

        $response->assertRedirect(route('register.pending'));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('users', [
            'name' => 'Alice Johnson',
            'email' => 'alice@wholesalebiz.test',
            'company_name' => 'Wholesale Tech Distributors LLC',
            'phone' => '+1-555-0145',
            'status' => 'pending',
            'is_admin' => false,
        ]);

        $createdUser = User::where('email', 'alice@wholesalebiz.test')->firstOrFail();
        $this->assertTrue(Hash::check('secret1234', $createdUser->password));
        $this->assertTrue($createdUser->isPending());
        $this->assertFalse($createdUser->isApproved());
        $this->assertFalse($createdUser->is_admin);

        // Verify pending confirmation view renders
        $pendingPage = $this->get(route('register.pending'));
        $pendingPage->assertOk();
        $pendingPage->assertSee('Registration Submitted');
        $pendingPage->assertSee('Pending Approval');
    }

    public function test_user_is_not_authenticated_after_registration(): void
    {
        $payload = [
            'name' => 'Bob Miller',
            'email' => 'bob@wholesalebiz.test',
            'company_name' => 'Apex Mobile Store',
            'phone' => '+1-555-0199',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ];

        $this->post(route('register'), $payload);

        $this->assertGuest();
    }

    public function test_invalid_registration_input_is_rejected(): void
    {
        $payload = [
            'name' => '', // required
            'email' => 'not-an-email', // invalid email
            'password' => 'short', // min 8
            'password_confirmation' => 'mismatch', // confirmed
        ];

        $response = $this->post(route('register'), $payload);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_duplicate_email_registration_is_rejected(): void
    {
        $existing = new User();
        $existing->name = 'Existing Partner';
        $existing->email = 'duplicate@wholesalebiz.test';
        $existing->password = Hash::make('password123');
        $existing->status = 'pending';
        $existing->is_admin = false;
        $existing->save();

        $payload = [
            'name' => 'Impostor',
            'email' => 'duplicate@wholesalebiz.test',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ];

        $response = $this->post(route('register'), $payload);

        $response->assertSessionHasErrors(['email']);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_registration_request_with_status_approved_or_is_admin_1_still_creates_pending_non_admin_user(): void
    {
        // Malicious client attempting to elevate privileges and bypass approval
        $maliciousPayload = [
            'name' => 'Mallory Hacker',
            'email' => 'mallory@exploit.test',
            'company_name' => 'Infiltrator Corp',
            'phone' => '+1-555-6666',
            'password' => 'supersecret123',
            'password_confirmation' => 'supersecret123',
            'status' => 'approved',
            'is_admin' => 1,
        ];

        $response = $this->post(route('register'), $maliciousPayload);

        $response->assertRedirect(route('register.pending'));

        $this->assertDatabaseHas('users', [
            'email' => 'mallory@exploit.test',
            'status' => 'pending',
            'is_admin' => false,
        ]);

        $user = User::where('email', 'mallory@exploit.test')->firstOrFail();
        $this->assertEquals('pending', $user->status, 'Status must remain pending despite payload tampering.');
        $this->assertFalse((bool) $user->is_admin, 'is_admin must remain false despite payload tampering.');
        $this->assertTrue($user->isPending());
        $this->assertFalse($user->isApproved());
    }

    public function test_register_link_is_present_in_layout(): void
    {
        $response = $this->get(route('catalog.index'));

        $response->assertOk();
        $response->assertSee(route('register'));
        $response->assertSee('Register');
    }

    public function test_catalog_still_shows_no_prices(): void
    {
        $this->seed(CategorySeeder::class);
        $this->seed(ProductSeeder::class);

        $response = $this->get(route('catalog.index'));

        $response->assertOk();
        $content = $response->getContent();

        $this->assertStringNotContainsString('$', $content);
        $this->assertStringNotContainsString('USD', $content);
        $this->assertStringNotContainsString('€', $content);
        $this->assertStringNotContainsString('799.00', $content);
        $this->assertStringNotContainsString('1250.00', $content);
    }
}
