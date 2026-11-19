<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@nursery.test'], [
            'name' => 'Nursery Administrator',
            'username' => 'nurseryadmin',
            'password' => Hash::make('Greenhouse2026!'),
        ]);
    }
}
