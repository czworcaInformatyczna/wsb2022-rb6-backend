<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\PasswordReset;
use Tests\TestCase;

class UserTest extends TestCase
{
    use WithFaker;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public User $user;
    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function login(){
        $response = $this->post('/api/login',[
            'email' => $this->user->email,
            'password' => 'password'
        ]);
        
        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) 
                => $json
                    ->whereType('name', 'string')
                    ->whereType('email', 'string')
                    ->whereType('access_token', 'string')
                    ->whereType('token_type', 'string')
                    ->whereType('theme_id', 'integer')
                    ->whereType('language_id', 'integer')
            );
    }

    /** @test */
    public function login_without_password(){
        $response = $this->post('/api/login',[
            'email' => $this->user->email
        ]);
        $response
            ->assertStatus(400)
            ->assertJsonStructure([
                'errors' => [
                    'password'
                ]
            ]);
    }

    /** @test */
    public function login_without_email(){
        $response = $this->post('/api/login',[
            'password' => 'password'
        ]);
        $response
            ->assertStatus(400)
            ->assertJsonStructure([
                'errors' => [
                    'email'
                ]
            ]);
    }

    /** @test */
    public function user_request_password_change(){
        $response = $this->post('/api/forgotpassword',[
            'email' => $this->user->email
        ]);
        $response
            ->assertStatus(200)
            ->assertExactJson([
                'message' => 'Email has been send',
            ]);

    }

    /** @test */
    public function user_request_password_change_empty_email(){
        $response = $this->post('/api/forgotpassword');
        $response
            ->assertStatus(400)
            ->assertJsonStructure([
                'errors' => [
                    'email'
                ]
            ]);
    }

    /** @test */
    public function reset_password(){
        $password = $this->faker->password();
        $token = Str::random();
        $this->post('/api/forgotpassword',[
            'email' => $this->user->email
        ]);
        PasswordReset::where('email', $this->user->email)->update([
            'token' => Hash::make($token)
        ]);
        $response = $this->patch('/api/resetpassword',[
            'email' => $this->user->email,
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $password
        ]);
        $response
            ->assertStatus(200)
            ->assertExactJson([
                'message' => 'Success',
            ]);
    }

    /** @test */
    public function reset_password_invalid_token(){
        $password = $this->faker->password();
        $response = $this->patch('/api/resetpassword',[
            'email' => $this->user->email,
            'token' => Str::random(10),
            'password' => $password,
            'password_confirmation' => $password
        ]);
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'message' => 'Bad data',
            ]);
    }

    /** @test */
    public function reset_password_lack_of_token(){
        $password = $this->faker->password();
        $response = $this->patch('/api/resetpassword',[
            'email' => $this->user->email,
            'password' => $password,
            'password_confirmation' => $password
        ]);
        $response
            ->assertStatus(400)
            ->assertJsonStructure([
                'errors' => [
                    'token'
                ]
            ]);
    }

    /** @test */
    public function reset_password_lack_of_email(){
        $password = $this->faker->password();
        $token = Str::random();
        $this->post('/api/forgotpassword',[
            'email' => $this->user->email
        ]);
        PasswordReset::where('email', $this->user->email)->update([
            'token' => Hash::make($token)
        ]);
        $response = $this->patch('/api/resetpassword',[
            'token' => $token,
            'password' => $token,
            'password_confirmation' => $token
        ]);
        $response
            ->assertStatus(400)
            ->assertJsonStructure([
                'errors' => [
                    'email'
                ]
            ]);
    }

    /** @test */
    public function reset_password_lack_of_password(){
        $password = $this->faker->password();
        $token = Str::random();
        $this->post('/api/forgotpassword',[
            'email' => $this->user->email
        ]);
        PasswordReset::where('email', $this->user->email)->update([
            'token' => Hash::make($token)
        ]);
        $response = $this->patch('/api/resetpassword',[
            'email' => $this->user->email,
            'token' => $token,
            'password_confirmation' => $password
        ]);
        $response
            ->assertStatus(400)
            ->assertJsonStructure([
                'errors' => [
                    'password'
                ]
            ]);
    }

    /** @test */
    public function reset_password_lack_of_password_confirmation(){
        $password = $this->faker->password();
        $token = Str::random();
        $this->post('/api/forgotpassword',[
            'email' => $this->user->email
        ]);
        PasswordReset::where('email', $this->user->email)->update([
            'token' => Hash::make($token)
        ]);
        $response = $this->patch('/api/resetpassword',[
            'email' => $this->user->email,
            'token' => $token,
            'password' => $password,
        ]);
        $response
            ->assertStatus(400)
            ->assertJsonStructure([
                'errors' => [
                    'password'
                ]
            ]);
    }

    /** @test */
    public function reset_password_passwords_dosent_match(){
        $token = Str::random();
        $password = $this->faker->password();
        $this->post('/api/forgotpassword',[
            'email' => $this->user->email
        ]);
        PasswordReset::where('email', $this->user->email)->update([
            'token' => Hash::make($token)
        ]);
        $response = $this->patch('/api/resetpassword',[
            'email' => $this->user->email,
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $password."1"
        ]);
        $response
            ->assertStatus(400)
            ->assertJsonStructure([
                'errors' => [
                    'password'
                ]
            ]);
    }

    /** @test */
    public function logout(){

        $response = $this->actingAs($this->user)->post('/api/logout');
        $response
            ->assertStatus(200)
            ->assertExactJson([
                'message' => "Tokens Revoked",
            ]);
    }

    public function tearDown(): void
    { 
        User::where('email', $this->user->email)->delete();
        parent::tearDown();
    }
}
