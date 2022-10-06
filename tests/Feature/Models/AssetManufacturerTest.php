<?php

namespace Tests\Feature\Models;

use App\Models\AssetManufacturer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;

class AssetManufacturerTest extends TestCase
{
    public User $user;
    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }
    /** @test */
    public function check_index()
    {

        AssetManufacturer::factory()->create();

        $response = $this->actingAs($this->user)->get('/api/asset_manufacturer');
        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                $json->each(
                    fn ($json) =>
                    $json->whereType('id', 'integer')
                        ->whereType('name', 'string')
                        ->whereType('created_at', 'string')
                        ->whereType('updated_at', 'string|null')
                )
            );
    }

    /** @test */
    public function check_store()
    {
        $name = Factory::create()->words(2, true);
        $response = $this->actingAs($this->user)->post('/api/asset_manufacturer', [
            "name" => $name
        ]);


        $response
        ->assertStatus(200)
        ->assertJson([
            'result' => true
        ]);

        $added = AssetManufacturer::where('id', $response->json()['model']['id'])->first();

        $this->assertTrue($added->name == $name);
    }

    /** @test */
    public function check_show()
    {
        $manufacturer = AssetManufacturer::factory()->create();

        $response = $this->actingAs($this->user)->get('/api/asset_manufacturer/' . $manufacturer->id);

        $response
            ->assertStatus(200)
            ->assertJson([
                'id' => $manufacturer->id,
                'name' => $manufacturer->name,
                'created_at' => Str::replace('"', '', json_encode($manufacturer->created_at)),
                'updated_at' => Str::replace('"', '', json_encode($manufacturer->updated_at))
            ]);
    }

    /** @test */
    public function check_update()
    {
        $manufacturer = AssetManufacturer::factory()->create();
        $new_name = Factory::create()->words(2, true);

        $response = $this->actingAs($this->user)->patch('/api/asset_manufacturer/' . $manufacturer->id, [
            'name' => $new_name
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'result' => 'success',
                'assetManufacturer' => [
                    'id' => $manufacturer->id,
                    'name' => $new_name,
                    'created_at' => Str::replace('"', '', json_encode($manufacturer->created_at)),
                    'updated_at' => Str::replace('"', '', json_encode($manufacturer->updated_at))
                ]
            ]);
    }

    /** @test */
    public function check_delete()
    {
        $manufacturer = AssetManufacturer::factory()->create();
        $manufacturer_id = $manufacturer->id;

        $response = $this->actingAs($this->user)->delete('/api/asset_manufacturer/' . $manufacturer->id);

        $response
            ->assertStatus(200)
            ->assertJson([
                'result' => true
            ]);

        $latest = AssetManufacturer::where('id', $manufacturer_id)->first();

        $this->assertTrue($latest == null);
    }
}
