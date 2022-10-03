<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\PasswordReset;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_remove_previous_testuser()
    {
        User::where('email', 'testuser@test.test')->delete();
        $this->assertTrue(true);
    }

    public function test_create_testuser(){

        $response = $this->post('/api/register', [
            'name' => 'testuser',
            'email' => 'testuser@test.test',
            'password' => 'testuser@test.test',
            'password_confirmation' => 'testuser@test.test'
        ]);

        $response->assertStatus(200);
    }

    public function test_login_as_testuser(){
        $response = $this->post('/api/login?password=testuser@test.test&email=testuser@test.test');
        $response->assertStatus(200);
    }

    public function test_login_without_password(){
        $response = $this->post('/api/login?email=testuser@test.test');
        $response->assertStatus(400);
    }

    public function test_login_without_email(){
        $response = $this->post('/api/login?password=testuser@test.test');
        $response->assertStatus(400);
    }

    public function test_user_change_theme(){
        $loginData = $this->post('/api/login?password=testuser@test.test&email=testuser@test.test');
        $response = $this->patch('/api/themes',[
            'value' => '2'
        ],[
            'Authorization' => 'Bearer '.$loginData['access_token']
        ]);
        $response->assertStatus(200);
    }

    public function test_user_change_language(){
        $loginData = $this->post('/api/login?password=testuser@test.test&email=testuser@test.test');
        $response = $this->patch('/api/languages',[
            'value' => '2'
        ],[
            'Authorization' => 'Bearer '.$loginData['access_token']
        ]);
        $response->assertStatus(200);
    }
    
    public function test_user_request_password_change(){
        $response = $this->post('/api/forgotpassword',[
            'email' => 'testuser@test.test'
        ]);
        $response->assertStatus(200);
    }

    public function test_user_request_password_change_empty_email(){
        $response = $this->post('/api/forgotpassword');
        $response->assertStatus(400);
    }

    public function test_user_set_password_reset(){
        PasswordReset::where('email', 'testuser@test.test')->update([
            'token' => Hash::make(1)
        ]);
        $this->assertTrue(true);
    }

    public function test_reset_password_invalid_token(){
        $response = $this->patch('/api/resetpassword',[
            'email' => 'testuser@test.test',
            'token' => '123',
            'password' => 'testuser@test.test2',
            'password_confirmation' => 'testuser@test.test2'
        ]);
        $response->assertStatus(400);
    }

    public function test_reset_password_lack_of_token(){
        $response = $this->patch('/api/resetpassword',[
            'email' => 'testuser@test.test',
            'password' => 'testuser@test.test2',
            'password_confirmation' => 'testuser@test.test2'
        ]);
        $response->assertStatus(400);
    }

    public function test_reset_password_lack_of_email(){
        $response = $this->patch('/api/resetpassword',[
            'token' => '1',
            'password' => 'testuser@test.test2',
            'password_confirmation' => 'testuser@test.test2'
        ]);
        $response->assertStatus(400);
    }

    public function test_reset_password_lack_of_password(){
        $response = $this->patch('/api/resetpassword',[
            'email' => 'testuser@test.test',
            'token' => '1',
            'password_confirmation' => 'testuser@test.test2'
        ]);
        $response->assertStatus(400);
    }

    public function test_reset_password_lack_of_password_confirmation(){
        $response = $this->patch('/api/resetpassword',[
            'email' => 'testuser@test.test',
            'token' => '1',
            'password' => 'testuser@test.test2',
        ]);
        $response->assertStatus(400);
    }

    public function test_reset_password_passwords_dosent_match(){
        $response = $this->patch('/api/resetpassword',[
            'email' => 'testuser@test.test',
            'token' => '1',
            'password' => 'testuser@test.test2',
            'password_confirmation' => 'testuser@test.test23'
        ]);
        $response->assertStatus(400);
    }

    public function test_reset_password(){
        $response = $this->patch('/api/resetpassword',[
            'email' => 'testuser@test.test',
            'token' => '1',
            'password' => 'testuser@test.test2',
            'password_confirmation' => 'testuser@test.test2'
        ]);
        $response->assertStatus(200);
    }
    
    public function test_check_new_password(){
        $response = $this->post('/api/login?password=testuser@test.test2&email=testuser@test.test');
        $response->assertStatus(200);
    }

    public function test_check_if_old_password_still_works(){
        $response = $this->post('/api/login?password=testuser@test.test&email=testuser@test.test');
        $response->assertStatus(401);
    }

    public function test_logout(){
        $loginData = $this->post('/api/login?password=testuser@test.test2&email=testuser@test.test');
        $response = $this->post('/api/logout',[],[
            'Authorization' => 'Bearer '.$loginData['access_token']
        ]);
        $response->assertStatus(200);
    }

    public function test_remove_account(){
        User::where('email', 'testuser@test.test')->delete();
        $this->assertTrue(true);
    }

    public function test_register_no_name_provided(){
        $response = $this->post('/api/register', [
            'email' => 'testuser@test.test',
            'password' => 'testuser@test.test',
            'password_confirmation' => 'testuser@test.test'
        ]);
        $response->assertStatus(400);
    }

    public function test_register_no_email_provided(){
        $response = $this->post('/api/register', [
            'name' => 'testuser',
            'password' => 'testuser@test.test',
            'password_confirmation' => 'testuser@test.test'
        ]);
        $response->assertStatus(400);
    }

    public function test_register_no_password_provided(){
        $response = $this->post('/api/register', [
            'name' => 'testuser',
            'email' => 'testuser@test.test',
            'password_confirmation' => 'testuser@test.test'
        ]);

        $response->assertStatus(400);
    }

    public function test_register_no_password_confirmation_provided(){
        $response = $this->post('/api/register', [
            'name' => 'testuser',
            'email' => 'testuser@test.test',
            'password' => 'testuser@test.test',
        ]);

        $response->assertStatus(400);
    }

    public function test_register_passwords_dosent_match(){

        $response = $this->post('/api/register', [
            'name' => 'testuser',
            'email' => 'testuser@test.test',
            'password' => 'testuser@test.test',
            'password_confirmation' => 'testuser@test.tes'
        ]);

        $response->assertStatus(400);
    }
}
