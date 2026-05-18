<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Tab;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $userRole = Role::where('name', 'user')->first();
        $adminRole = Role::where('name', 'admin')->first();

        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@guitartabs.test',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
        ]);

        $demo = User::create([
            'name' => 'Demo User',
            'email' => 'demo@guitartabs.test',
            'password' => Hash::make('password'),
            'role_id' => $userRole->id,
        ]);

        Tab::create([
            'user_id' => $demo->id,
            'title' => 'Wonderwall',
            'artist' => 'Oasis',
            'content' => "Em7  G  D  A7sus4\n\nToday is gonna be the day...\n\n[Verse]\nEm7        G\nToday is gonna be the day",
        ]);

        Tab::create([
            'user_id' => $demo->id,
            'title' => 'Blackbird',
            'artist' => 'The Beatles',
            'content' => "G  Am7  G/B  G\n\nBlackbird singing in the dead of night\n\n[Verse]\nG           Am7\nBlackbird singing in the dead of night",
        ]);
    }
}
