<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            [
                'name' => 'dashcode admin',
                'email' => 'dashcode@gmail.com',
                'password' => Hash::make(12345678),
            ]
        ]);

        User::insert([
            [
                'name' => 'Blast admin',
                'email' => 'admin@blast.com',
                'password' => Hash::make(12345678),
            ]
        ]);
    }
}
