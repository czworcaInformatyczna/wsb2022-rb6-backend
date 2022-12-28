<?php

use Spatie\Permission\Models\Permission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permissions = [
            'Manage Roles',
            'Manage Users',
            'Manage Licences',
            'Manage Assets',
            'Manage Categories',
            'Manage Manufacturers',
            'Manage Components'
        ];

        foreach ($permissions as $permission) {
            if (!Permission::where('name', $permission)->first()) {
                Permission::create([
                    'name' => $permission
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $permissions = [
            'Manage Roles',
            'Manage Users',
            'Manage Licences',
            'Manage Assets',
            'Manage Categories',
            'Manage Manufacturers',
            'Manage Components'
        ];

        foreach ($permissions as $permission) {
            Permission::where('name', $permission)->delete();
        }
    }
};
