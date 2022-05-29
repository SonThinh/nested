<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::query()->create([
            'login_id'      => 'user',
            'password'      => 'password',
            'name'          => 'user',
            'email'         => 'user@gmail.com',
            'furigana_name' => 'user',
            'unique_code'   => generateUniqueCode(),
        ]);
        User::factory(400)->create();
    }
}
