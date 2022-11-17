<?php

namespace Tests\Unit;

use App\Models\Asset;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Support\Str;

class AssetControllerTest extends TestCase
{
    public function test_index_has_correct_data_types()
    {
        Asset::factory()->create();

        $response = $this->actingAs(self::getRandomUser())->get('/api/asset');
        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) =>
                TestCase::assertableJsonPaginationTest($json, [
                    'data.0.id' => 'integer',
                    'data.0.created_at' => 'string',
                    'data.0.updated_at' => 'string',
                    'data.0.name' => 'string',
                    'data.0.tag' => 'string',
                    'data.0.asset_model_id' => 'integer',
                    'data.0.image' => 'string',
                    'data.0.serial' => 'string',
                    'data.0.status' => 'integer',
                    'data.0.current_holder_id' => 'integer|null',
                    'data.0.notes' => 'string|null',
                    'data.0.warranty' => 'integer|null',
                    'data.0.purchase_date' => 'string|null',
                    'data.0.order_number' => 'string|null',
                    'data.0.price' => 'string|null',
                    'data.0.asset_model' => 'array',
                    'data.0.current_holder' => 'array|null',
                ])
            );
    }
}
