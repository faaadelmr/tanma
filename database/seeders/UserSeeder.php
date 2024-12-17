<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'saya adalah admin',
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin')
        ]);
        $admin->assignRole('admin');

        $pengguna = User::create([
            'name' => 'saya ini pengguna',
            'username' => 'pengguna',
            'email' => 'pengguna@gmail.com',
            'password' => bcrypt('pengguna')
        ]);
        $pengguna->assignRole('pengguna');

        $pengguna2 = User::create([
            'name' => 'Pengguna Dua',
            'username' => 'pengguna2',
            'email' => 'pengguna2@gmail.com',
            'password' => bcrypt('pengguna')
        ]);
        $pengguna2->assignRole('pengguna');

        $pengguna3 = User::create([
            'name' => 'Pengguna Tiga',
            'username' => 'pengguna3', 
            'email' => 'pengguna3@gmail.com',
            'password' => bcrypt('pengguna')
        ]);
        $pengguna3->assignRole('pengguna');

        $pengguna4 = User::create([
            'name' => 'Pengguna Empat',
            'username' => 'pengguna4',
            'email' => 'pengguna4@gmail.com',
            'password' => bcrypt('pengguna')
        ]);
        $pengguna4->assignRole('pengguna');

        $pengguna5 = User::create([
            'name' => 'Pengguna Lima',
            'username' => 'pengguna5',
            'email' => 'pengguna5@gmail.com',
            'password' => bcrypt('pengguna')
        ]);
        $pengguna5->assignRole('pengguna');

        $pengguna6 = User::create([
            'name' => 'Pengguna Enam',
            'username' => 'pengguna6',
            'email' => 'pengguna6@gmail.com',
            'password' => bcrypt('pengguna')
        ]);
        $pengguna6->assignRole('pengguna');
    }
}
