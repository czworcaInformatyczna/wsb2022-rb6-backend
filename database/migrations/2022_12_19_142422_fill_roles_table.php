<?php

use Spatie\Permission\Models\Role;
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
        if (!Role::where('name', 'Super Admin')->first()) {
            Role::create([
                'name' => 'Super Admin',
                'guard_name' => 'web'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Role::where('name', 'Super Admin')->first()) {
            Role::where('name', 'Super Admin')->delete();
        }
    }
};
