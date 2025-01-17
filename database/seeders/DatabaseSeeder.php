<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Ridwan Halim',
            'email' => '190102hr@gmail.com',
            'role' => 'admin',
            'profilepict' => env('IMAGE_DEFAULT'),
            'password' => bcrypt('Point.co190102'),
            'email_verified_at' => now(),
        ]);

        User::factory()->create([
            'name' => 'Halim',
            'email' => 'halim.ridwan.19.01@gmail.com',
            'role' => 'pasien',
            'profilepict' => env('IMAGE_DEFAULT'),
            'password' => bcrypt('Halim12345.'),
            'email_verified_at' => now(),
        ]);
    }
}
