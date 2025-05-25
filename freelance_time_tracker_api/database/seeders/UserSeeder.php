<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->count(10)->create([
            'password' => Hash::make('password'), 
        ]);
        User::factory()->create([
            'name' => 'Asieb',
            'email' => 'asieb.hasan.supto@gmail.com',
            'password' => Hash::make('password'),
        ]);
    }
}
