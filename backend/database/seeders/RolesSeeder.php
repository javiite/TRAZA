<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run ():void
    { 
        foreach (['Admin','Gerencia','Administracion','ChoferTecnico'] as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        $admin = User::firstOrCreate(
            ['email' => 'admin@traza.local'],
            ['name' => 'Admin', 'password' => Hash::make('password')]
        );

        if (!$admin->hasRole('Admin')) {
            $admin->assignRole('Admin');
        }
    }
 
}
