<?php

namespace Database\Seeders;


use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dandy = User::create([
            'name' => 'fadel',
            'username' => 'fadel',
            'email' => 'fadel@gmail.com',
            'password' => bcrypt('fadel')
        ]);
        $dandy->assignRole('admin');

        $dandy = User::create([
            'name' => 'Dandy',
            'username' => 'dandy',
            'email' => 'dandy@gmail.com',
            'password' => bcrypt('dandy')
        ]);
        $dandy->assignRole('admin');

        $eva = User::create([
            'name' => 'Evan',
            'username' => 'evan',
            'email' => 'eva@gmail.com',
            'password' => bcrypt('evan')
        ]);
        $eva->assignRole('pengguna');

        $ryand = User::create([
            'name' => 'Ryand',
            'username' => 'ryand',
            'email' => 'ryand@gmail.com',
            'password' => bcrypt('ryand')
        ]);
        $ryand->assignRole('pengguna');


        // $faker = Faker::create('id_ID');
        // for ($i = 1; $i <= 9; $i++) {
        //     $user = User::create([
        //         'name' => $faker->name,
        //         'username' => $faker->unique()->userName,
        //         'email' => $faker->unique()->safeEmail,
        //         'password' => bcrypt('pengguna')
        //     ]);
        //     $user->assignRole('pengguna');
        // }

    }
}
