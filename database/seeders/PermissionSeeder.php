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
            'Manage Roles',
            'Manage Users',
            'Manage Licences',
            'Manage Assets',
            'Manage Categories',
            'Manage Manufacturers',
            'Manage Models',
            'Manage Components'
        ];

        foreach ($permissions as $permission) {
            if (!Permission::where('name', $permission)->first()) {
                Permission::create([
                    'name' => $permission,
                    'guard_name' => 'web'
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
