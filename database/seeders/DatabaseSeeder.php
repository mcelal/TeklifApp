<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->insert([
            'name'     => 'DemoAdmin',
            'email'    => 'demo@demo.test',
            'password' => \Hash::make('password')
        ]);
        // \App\Models\User::factory(10)->create();
    }
}
