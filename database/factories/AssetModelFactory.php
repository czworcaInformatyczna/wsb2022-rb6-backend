<?php

namespace Database\Factories;

use App\Models\AssetCategory;
use App\Models\Manufacturer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AssetModel>
 */
class AssetModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $category = AssetCategory::inRandomOrder()->first();
        $manufacturer = Manufacturer::inRandomOrder()->first();
        return [
            'name' => Str::ucfirst($this->faker->unique()->words(2, true)),
            'asset_category_id' => $category->id,
            'manufacturer_id' => $manufacturer->id
        ];
    }
}
