<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'permission1',
            'permission2',
            'permission3'
        ];

        foreach($permissions as $permission){
            Permission::create([
                'name' => $permission
            ]);
        }
    }
}
