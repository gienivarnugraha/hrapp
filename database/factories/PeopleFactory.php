<?php

namespace Database\Factories;

use App\Models\JobTitle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\People>
 */
class PeopleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'nik' => fake()->unique()->numerify('######'),
            'org' => fake()->regexify('[A-Z]{2}[0-9]{4}'),
            'job_title_id' => JobTitle::all()->random()->id,
            //'email' => fake()->randomElement(['siti.rahma.darya@gmail.com','nivar.nugraha@gmail.com']),
            'email' => 'siti.rahma.darya@gmail.com',
            'position' => fake()->randomElement(['junior','medior','senior']),
        ];
    }
}
