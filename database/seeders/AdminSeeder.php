<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::query()->create([
            'login_id'      => 'admin',
            'password'      => 'password',
            'name'          => 'admin',
            'email'         => 'admin@gmail.com',
            'furigana_name' => 'admin',
        ]);
    }
}
