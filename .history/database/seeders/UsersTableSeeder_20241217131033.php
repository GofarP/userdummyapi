<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use \App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create(); // Membuat instance Faker

        // Menambahkan data palsu ke dalam tabel users
        foreach (range(1, 50) as $index) {
            User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'), // Menggunakan password default
                'address' => $faker->address,
                'phone' => $faker->phoneNumber,
            ]);
        }

    }
}
