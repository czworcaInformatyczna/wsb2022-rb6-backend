<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RouteTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_resetpassword()
    {
        $response = $this->get('/resetpassword');
        $response->assertStatus(200);
    }

    public function test_get_themes(){
        $response = $this->get('/api/themes');
        $response->assertStatus(200);
    }

    public function test_get_languages(){
        $response = $this->get('/api/languages');
        $response->assertStatus(200);
    }
}
