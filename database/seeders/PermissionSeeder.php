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

        foreach (Permission::all() as $permissionInDatabase) {
            if (!$this->checkIfIsInArray($permissionInDatabase->name, $permissions)) {
                $permissionInDatabase->delete();
            }
        }
    }

    public function checkIfIsInArray($checked, $array)
    {
        foreach ($array as $value) {
            if ($checked == $value) {
                return true;
            }
        }
        return false;
    }
}
