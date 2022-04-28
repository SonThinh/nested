<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        return [
            'login_id'      => $this->faker->userName,
            'password'      => 'password',
            'name'          => $this->faker->name(),
            'email'         => $this->faker->email,
            'furigana_name' => $this->faker->name(),
            'unique_code'   => generateUniqueCode(),
        ];
    }
}
