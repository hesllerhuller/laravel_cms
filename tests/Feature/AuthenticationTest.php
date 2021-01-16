<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Factories\UserFactory;

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

        $this->withoutExceptionHandling();

        $password = bcrypt('123456789');

        $response = $this->post('/auth/register-v2', [
            'name' => 'Hesller Huller',
            'email' => 'exemplo@gmail.com',
            'password' => $password,
            'password_confirmation' => $password,
        ] );

        dd($response->status());

        $response->assertStatus(201);
    }

    public function test_system_redirect_user_to_verify_email()
    {
        $response = $this->get('/email/verify');
        dd($response);
    }

    public function test_login_user()
    {
        $user = User::create([
            'name' => 'Hadesh',
            'email'=>'hadesh@gmail.com',
            'password'=>bcrypt('123456789'),
        ]);

        $response = $this->actingAs($user)->get('/login');


        dd($response->getContent());
        $response->assertStatus(200);
    }
}
