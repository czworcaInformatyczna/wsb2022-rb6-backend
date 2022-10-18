<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Theme;
use App\Models\User;

class ThemeTest extends TestCase
{
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
    public function user_change_theme(){
        $response = $this->actingAs($this->user)->patch('/api/themes',[
            'value' => Theme::orderBy('id', 'desc')->first('id')->id
        ]);
        $response
            ->assertStatus(200)
            ->assertExactJson([
                'theme_id' => Theme::orderBy('id', 'desc')->first('id')->id,
            ]);
    }

    /** @test */
    public function user_change_theme_unlogged(){
        $response = $this->patch('/api/themes',[
            'value' => Theme::orderBy('id', 'desc')->first('id')->id
        ]);
        $response
            ->assertStatus(401)
            ->assertExactJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    /** @test */
    public function user_change_theme_to_not_existing(){
        $response = $this->actingAs($this->user)->patch('/api/themes',[
            'value' => Theme::orderBy('id', 'desc')->first('id')->id+1
        ]);
        $response
            ->assertStatus(400)
            ->assertExactJson([
                'message' => 'Invalid theme_id',
            ]);
    }

    public function tearDown(): void
    { 
        User::where('email', $this->user->email)->delete();
        parent::tearDown();
    }
}
