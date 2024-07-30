<?php

namespace Database\Factories;


use Illuminate\Support\Str;


use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'user_id' => 1,
            'korwil_id' => random_int(1, 3),
            'address' => 'Badung',
            'gender' => 'Laki-laki',
            'telp' => random_int(100000, 200000),
        ];
    }
}
