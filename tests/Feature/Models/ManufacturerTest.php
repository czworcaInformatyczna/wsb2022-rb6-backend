<?php

namespace Tests\Feature\Models;

use App\Models\Manufacturer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;

class ManufacturerTest extends TestCase
{
    /** @test */
    public function check_index()
    {

        Manufacturer::factory()->create();

        $response = $this->actingAs(self::getRandomUser())->get('/api/manufacturer');
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
        $response = $this->actingAs(self::getRandomUser())->post('/api/manufacturer', [
            "name" => $name
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'result' => true
            ]);

        $added = Manufacturer::where('id', $response->json()['model']['id'])->first();

        $this->assertTrue($added->name == $name);
    }

    /** @test */
    public function check_show()
    {
        $manufacturer = Manufacturer::factory()->create();

        $response = $this->actingAs(self::getRandomUser())->get('/api/manufacturer/' . $manufacturer->id);

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
        $manufacturer = Manufacturer::factory()->create();
        $new_name = Factory::create()->words(2, true);

        $response = $this->actingAs(self::getRandomUser())->patch('/api/manufacturer/' . $manufacturer->id, [
            'name' => $new_name
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'result' => 'success',
                'manufacturer' => [
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
        $manufacturer = Manufacturer::factory()->create();
        $manufacturer_id = $manufacturer->id;

        $response = $this->actingAs(self::getRandomUser())->delete('/api/manufacturer/' . $manufacturer->id);

        $response
            ->assertStatus(200)
            ->assertJson([
                'result' => true
            ]);

        $latest = Manufacturer::where('id', $manufacturer_id)->first();

        $this->assertTrue($latest == null);
    }
}
