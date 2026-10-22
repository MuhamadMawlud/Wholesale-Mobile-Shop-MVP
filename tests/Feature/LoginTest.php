<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('Wholesale Customer Login');
        $response->assertSee('Business Email Address');
        $response->assertSee('Password');
        $response->assertSee('Remember my business session');
    }

    public function test_approved_user_can_log_in_and_is_redirected_to_catalog(): void
    {
        $user = new User;
        $user->name = 'Approved Partner';
        $user->email = 'approved@wholesalebiz.test';
        $user->password = Hash::make('secret1234');
        $user->status = 'approved';
        $user->is_admin = false;
        $user->save();

        $response = $this->post(route('login'), [
            'email' => 'approved@wholesalebiz.test',
            'password' => 'secret1234',
        ]);

        $response->assertRedirect(route('catalog.index'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_pending_user_cannot_log_in_and_no_session_is_created(): void
    {
        $user = new User;
        $user->name = 'Pending Partner';
        $user->email = 'pending@wholesalebiz.test';
        $user->password = Hash::make('secret1234');
        $user->status = 'pending';
        $user->is_admin = false;
        $user->save();

        $response = $this->post(route('login'), [
            'email' => 'pending@wholesalebiz.test',
            'password' => 'secret1234',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'Your account is pending administrator approval.',
        ]);
        $this->assertGuest();
    }

    public function test_rejected_user_cannot_log_in_and_no_session_is_created(): void
    {
        $user = new User;
        $user->name = 'Rejected Applicant';
        $user->email = 'rejected@wholesalebiz.test';
        $user->password = Hash::make('secret1234');
        $user->status = 'rejected';
        $user->is_admin = false;
        $user->save();

        $response = $this->post(route('login'), [
            'email' => 'rejected@wholesalebiz.test',
            'password' => 'secret1234',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'Your account registration has been rejected. Please contact support.',
        ]);
        $this->assertGuest();
    }

    public function test_wrong_password_fails_and_does_not_reveal_email_existence(): void
    {
        $user = new User;
        $user->name = 'Existing User';
        $user->email = 'existing@wholesalebiz.test';
        $user->password = Hash::make('correctpassword');
        $user->status = 'approved';
        $user->is_admin = false;
        $user->save();

        $response = $this->post(route('login'), [
            'email' => 'existing@wholesalebiz.test',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'These credentials do not match our records.',
        ]);
        $this->assertGuest();
    }

    public function test_non_existent_email_fails_with_same_generic_credential_error(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'doesnotexist@wholesalebiz.test',
            'password' => 'anypassword',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'These credentials do not match our records.',
        ]);
        $this->assertGuest();
    }

    public function test_authenticated_user_can_log_out_and_session_is_invalidated(): void
    {
        $user = new User;
        $user->name = 'Active Partner';
        $user->email = 'active@wholesalebiz.test';
        $user->password = Hash::make('secret1234');
        $user->status = 'approved';
        $user->is_admin = false;
        $user->save();

        $this->actingAs($user);
        $this->assertAuthenticated();

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('catalog.index'));
        $this->assertGuest();
    }

    public function test_catalog_shows_prices_for_logged_in_approved_user(): void
    {
        $this->seed(CategorySeeder::class);
        $this->seed(ProductSeeder::class);

        $user = new User;
        $user->name = 'Approved Customer';
        $user->email = 'customer@wholesalebiz.test';
        $user->password = Hash::make('secret1234');
        $user->status = 'approved';
        $user->is_admin = false;
        $user->save();

        $response = $this->actingAs($user)->get(route('catalog.index'));

        $response->assertOk();
        $content = $response->getContent();

        // Approved customer sees wholesale prices in T5
        $this->assertStringContainsString('$', $content);
        $this->assertStringContainsString('799.00', $content);

        // Check product detail
        $detailResponse = $this->actingAs($user)->get('/products/apex-pro-5g-256gb-titanium');
        $detailResponse->assertOk();
        $detailContent = $detailResponse->getContent();

        $this->assertStringContainsString('$', $detailContent);
        $this->assertStringContainsString('799.00', $detailContent);
    }

    public function test_layout_navbar_shows_correct_links_based_on_auth_state(): void
    {
        // As Guest
        $guestResponse = $this->get(route('catalog.index'));
        $guestResponse->assertOk();
        $guestResponse->assertSee(route('login'));
        $guestResponse->assertSee(route('register'));
        $guestResponse->assertDontSee(route('logout'));

        // As Approved User
        $user = new User;
        $user->name = 'Tech Retailers Group';
        $user->email = 'retailers@wholesalebiz.test';
        $user->password = Hash::make('secret1234');
        $user->status = 'approved';
        $user->is_admin = false;
        $user->save();

        $authResponse = $this->actingAs($user)->get(route('catalog.index'));
        $authResponse->assertOk();
        $authResponse->assertSee('Tech Retailers Group');
        $authResponse->assertSee('Approved');
        $authResponse->assertSee(route('logout'));
        $authResponse->assertDontSee(route('login'));
    }
}
