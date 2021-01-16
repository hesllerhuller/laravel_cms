<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Factories\BackupUserFactory;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    public function test_user_can_view_registration_form()
    {
        $this->withoutExceptionHandling();

        $response = $this->get('/auth/register-v2');

        $response->assertSuccessful();
        $response->assertViewIs('.content.authentication.auth-register-v2');

    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_user_can_create_account()
    {

//        $this->withoutExceptionHandling();

        $password = bcrypt('123456789');

        $response = $this->post('/auth/register-v2', [
            'name' => 'Hesller Huller',
            'email' => 'exemplo@gmail.com',
            'password' => $password,
            'password_confirmation' => $password,
        ] );

        $response->assertRedirect('/email/verify');
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', ['name' => 'Hesller Huller', 'email' => 'exemplo@gmail.com',
            'email_verified_at'=>null
        ]);

    }

    public function test_system_redirect_user_to_verify_email()
    {

        $password = bcrypt('123456789');

        $response = $this->post('/auth/register-v2', [
            'name' => 'Hesller Huller',
            'email' => 'exemplo@gmail.com',
            'password' => $password,
            'password_confirmation' => $password,
        ] );

        $response->assertRedirect('/email/verify');
    }

    public function test_user_can_not_login_if_email_not_verified()
    {

        $this->withoutExceptionHandling();

//        $password = bcrypt('123456789');

//        $user = User::create([
//            'name' => 'Hadesh',
//            'email'=>'hadesh@gmail.com',
//            'password'=>$password,
//        ]);
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => '123456789'
        ]);

        $response->assertRedirect('/email/verify');
        $this->assertAuthenticatedAs($user);
    }

    public function test_if_user_with_verified_email_is_redirected_to_dashboard()
    {
        $this->withoutExceptionHandling();

        // assert user with verified email is redirected to dashboard
        $user = User::factory()->create([]);
        $response = $this->actingAs($user)->get('/email/verify');
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);

        // assert user with unverified email is not redirect, but stays in email verification page notice
        $user = User::factory()->create(['email_verified_at'=>null]);
        $response = $this->actingAs($user)->get('/email/verify');
        $response->assertViewIs(route('verification.notice'));
        $this->assertAuthenticatedAs($user);

    }
}
