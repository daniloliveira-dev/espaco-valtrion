<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'medical_record_number' => 'VT-'.$this->faker->unique()->numerify('######'),
            'name' => $this->faker->name(),
            'date_of_birth' => $this->faker->dateTimeBetween('-85 years', '-18 years')->format('Y-m-d'),
            'email' => $this->faker->optional()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
        ];
    }
}
