<?php

namespace Database\Seeders;

use App\Models\AssetManufacturer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssetManufacturerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AssetManufacturer::factory()->count(10)->create();
    }
}
