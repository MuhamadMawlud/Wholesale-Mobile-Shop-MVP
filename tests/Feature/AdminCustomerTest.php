<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminCustomerTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(array $attributes = []): User
    {
        $user = new User;
        $user->name = $attributes['name'] ?? 'Admin User';
        $user->email = $attributes['email'] ?? 'admin@wholesale.test';
        $user->company_name = 'Wholesale Admin Corp';
        $user->phone = '+1-555-0100';
        $user->password = Hash::make($attributes['password'] ?? 'admin1234');
        $user->status = 'approved';
        $user->is_admin = true;
        $user->save();

        return $user;
    }

    private function createCustomer(string $status = 'pending', array $attributes = []): User
    {
        $user = new User;
        $user->name = $attributes['name'] ?? 'Sample Customer';
        $user->email = $attributes['email'] ?? 'customer@wholesalebiz.test';
        $user->company_name = $attributes['company_name'] ?? 'Sample Biz LLC';
        $user->phone = $attributes['phone'] ?? '+1-555-0123';
        $user->password = Hash::make($attributes['password'] ?? 'secret1234');
        $user->status = $status;
        $user->is_admin = false;
        $user->save();

        return $user;
    }

    public function test_guest_is_redirected_to_login_when_accessing_admin_customers(): void
    {
        $response = $this->get(route('admin.customers.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_when_posting_to_approve_or_reject(): void
    {
        $customer = $this->createCustomer('pending');

        $approveResponse = $this->post(route('admin.customers.approve', $customer));
        $approveResponse->assertRedirect(route('login'));

        $rejectResponse = $this->post(route('admin.customers.reject', $customer));
        $rejectResponse->assertRedirect(route('login'));
    }

    public function test_authenticated_non_admin_gets_403_on_admin_customer_list(): void
    {
        $customer = $this->createCustomer('approved');

        $response = $this->actingAs($customer)->get(route('admin.customers.index'));

        $response->assertForbidden();
    }

    public function test_authenticated_non_admin_gets_403_when_attempting_approve_or_reject(): void
    {
        $customer = $this->createCustomer('approved');
        $target = $this->createCustomer('pending', ['email' => 'target@wholesalebiz.test']);

        $approveResponse = $this->actingAs($customer)->post(route('admin.customers.approve', $target));
        $approveResponse->assertForbidden();

        $rejectResponse = $this->actingAs($customer)->post(route('admin.customers.reject', $target));
        $rejectResponse->assertForbidden();

        $target->refresh();
        $this->assertEquals('pending', $target->status);
    }

    public function test_admin_sees_customer_list_with_details_and_pending_first(): void
    {
        $admin = $this->createAdmin();

        $approvedCust = $this->createCustomer('approved', [
            'name' => 'Alpha Approved LLC',
            'email' => 'alpha@approved.test',
            'company_name' => 'Alpha Wholesale Corp',
            'phone' => '+1-555-1111',
        ]);

        $pendingCust = $this->createCustomer('pending', [
            'name' => 'Beta Pending Co',
            'email' => 'beta@pending.test',
            'company_name' => 'Beta Electronics Inc',
            'phone' => '+1-555-2222',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.customers.index'));

        $response->assertOk();
        $response->assertSee('Customer Registrations');

        // Verify details are rendered
        $response->assertSee($pendingCust->name);
        $response->assertSee($pendingCust->email);
        $response->assertSee($pendingCust->company_name);
        $response->assertSee($pendingCust->phone);
        $response->assertSee('Pending');

        $response->assertSee($approvedCust->name);
        $response->assertSee($approvedCust->email);
        $response->assertSee('Approved');

        // Verify admin is NOT in the customer list
        $customersInView = $response->viewData('customers');
        $this->assertFalse($customersInView->contains('id', $admin->id));

        // Verify pending customer is listed first
        $this->assertEquals($pendingCust->id, $customersInView->first()->id);
    }

    public function test_admin_approve_action_sets_status_to_approved(): void
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer('pending', ['email' => 'pending@test.com']);

        $response = $this->actingAs($admin)->post(route('admin.customers.approve', $customer));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $customer->refresh();
        $this->assertEquals('approved', $customer->status);
        $this->assertTrue($customer->isApproved());
    }

    public function test_admin_reject_action_sets_status_to_rejected(): void
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer('pending', ['email' => 'rejected@test.com']);

        $response = $this->actingAs($admin)->post(route('admin.customers.reject', $customer));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $customer->refresh();
        $this->assertEquals('rejected', $customer->status);
        $this->assertTrue($customer->isRejected());
    }

    public function test_approved_customer_can_then_log_in(): void
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer('pending', [
            'email' => 'candidate@wholesalebiz.test',
            'password' => 'secret1234',
        ]);

        // First attempt login while pending -> must fail
        $loginAttempt1 = $this->post(route('login'), [
            'email' => 'candidate@wholesalebiz.test',
            'password' => 'secret1234',
        ]);
        $loginAttempt1->assertSessionHasErrors(['email']);
        $this->assertGuest();

        // Admin approves the candidate
        $this->actingAs($admin)->post(route('admin.customers.approve', $customer));
        auth()->logout();

        // Customer logs in now -> must succeed
        $loginAttempt2 = $this->post(route('login'), [
            'email' => 'candidate@wholesalebiz.test',
            'password' => 'secret1234',
        ]);
        $loginAttempt2->assertRedirect(route('catalog.index'));
        $this->assertAuthenticatedAs($customer);
    }

    public function test_rejected_customer_still_cannot_log_in(): void
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer('pending', [
            'email' => 'rejected-client@wholesalebiz.test',
            'password' => 'secret1234',
        ]);

        // Admin rejects customer
        $this->actingAs($admin)->post(route('admin.customers.reject', $customer));
        auth()->logout();

        // Customer attempts login -> must fail
        $loginAttempt = $this->post(route('login'), [
            'email' => 'rejected-client@wholesalebiz.test',
            'password' => 'secret1234',
        ]);
        $loginAttempt->assertSessionHasErrors([
            'email' => 'Your account registration has been rejected. Please contact support.',
        ]);
        $this->assertGuest();
    }

    public function test_admin_cannot_change_own_status(): void
    {
        $admin = $this->createAdmin();

        $approveResponse = $this->actingAs($admin)->post(route('admin.customers.approve', $admin));
        $approveResponse->assertForbidden();

        $rejectResponse = $this->actingAs($admin)->post(route('admin.customers.reject', $admin));
        $rejectResponse->assertForbidden();

        $admin->refresh();
        $this->assertEquals('approved', $admin->status);
    }

    public function test_admin_cannot_change_other_admins_status(): void
    {
        $admin1 = $this->createAdmin(['email' => 'admin1@wholesale.test']);
        $admin2 = $this->createAdmin(['name' => 'Second Admin', 'email' => 'admin2@wholesale.test']);

        $response = $this->actingAs($admin1)->post(route('admin.customers.reject', $admin2));
        $response->assertForbidden();

        $admin2->refresh();
        $this->assertEquals('approved', $admin2->status);
    }

    public function test_customers_link_in_layout_visible_only_to_admin(): void
    {
        // 1. Guest view
        $guestResponse = $this->get(route('catalog.index'));
        $guestResponse->assertDontSee(route('admin.customers.index'));

        // 2. Non-admin customer view
        $customer = $this->createCustomer('approved');
        $customerResponse = $this->actingAs($customer)->get(route('catalog.index'));
        $customerResponse->assertDontSee(route('admin.customers.index'));

        // 3. Admin view
        $admin = $this->createAdmin();
        $adminResponse = $this->actingAs($admin)->get(route('catalog.index'));
        $adminResponse->assertSee(route('admin.customers.index'));
        $adminResponse->assertSee('Customers');
    }

    public function test_admin_user_seeder_reads_env_and_creates_admin(): void
    {
        $this->seed(AdminUserSeeder::class);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@wholesale.test',
            'status' => 'approved',
            'is_admin' => true,
        ]);

        $admin = User::where('email', 'admin@wholesale.test')->firstOrFail();
        $this->assertTrue(Hash::check(env('ADMIN_PASSWORD', 'admin1234'), $admin->password));
    }
}
