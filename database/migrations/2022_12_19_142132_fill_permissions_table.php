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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
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
            Permission::where('name', $permission)->delete();
        }
    }
};
