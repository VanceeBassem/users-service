<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole = Role::firstWhere('name', 'admin');
        if (!$adminRole) {
            $adminRole = Role::create(['name'=>'admin']);
        }

        User::updateOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Admin',
            'password' => Hash::make('secret123'),
            'role_id' => $adminRole->id,
        ]);
    }
}
