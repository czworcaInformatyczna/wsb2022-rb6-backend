<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
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
        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'email@example.com',
            'password' => Hash::make('admin')
        ]);

        $user->syncRoles(['Super Admin']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        User::where('name', 'Super Admin')->delete();
    }
};
