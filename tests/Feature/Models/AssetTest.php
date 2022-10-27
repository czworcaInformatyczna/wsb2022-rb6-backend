<?php

namespace Tests\Feature\Models;

use App\Http\Controllers\AssetController;
use App\Models\Asset;
use App\Models\User;
use Database\Factories\AssetFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

use function PHPUnit\Framework\assertTrue;

class AssetTest extends TestCase
{
    public User $user;
    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function test_success_parse_image()
    {
        $randomImageBase64 = Arr::random(AssetFactory::$sampleImages);
        $uploadedImageName = AssetController::parse_image($randomImageBase64);
        $fileFormat = explode('.', $uploadedImageName)[1];
        $file = Storage::disk('public')->get($uploadedImageName);
        $fileBase64 = 'data:image/'.$fileFormat.';base64,'.base64_encode($file);
        assertTrue($randomImageBase64 == $fileBase64);
    }

    /** @test */
    // public function test_success_store()
    // {
    //     $model = Asset::factory(1)->without_image()->make()->first();
    //     dd($model->current_holder_id, $model->toArray());
    //     $model->image = Arr::random(AssetFactory::$sampleImages);
    //     $response = $this->actingAs($this->user)->post('/api/asset', $model->toArray());

    //     $correctResponse = $model->toArray();
    //     $correctResponse['id'] = $response->json()['model']['id'];
    //     $correctResponse['image'] = $response->json()['model']['image'];

    //     $response
    //     ->assertStatus(200)
    //     ->assertJson([
    //         'result' => true,
    //         'model' => $correctResponse
    //     ]);
    // }
}
