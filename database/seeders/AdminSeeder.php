<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $u = User::firstOrNew(['email' => 'admin@bbws.test']);
        $u->name = 'Administrator BBWS';
        $u->password = Hash::make('Admin#2025');
        $u->role = 'admin';
        $u->email_verified_at = now();
        $u->save();
    }
}