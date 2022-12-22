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
            'Show Roles',
            'Manage Roles',
            'Show Users',
            'Manage Users',
            'Show Licences',
            'Manage Licences',
            'Show Assets',
            'Manage Assets',
            'Show Categories',
            'Show Logs',
            'Show Manufacturers',
            'Manage Manufacturers',
        ];

        foreach ($permissions as $permission) {
            if (!Permission::where('name', $permission)->first()) {
                Permission::create([
                    'name' => $permission
                ]);
            }
        }
    }
}
