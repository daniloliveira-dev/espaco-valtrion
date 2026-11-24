<?php

namespace Database\Factories;

use App\Models\Physician;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Physician>
 */
class PhysicianFactory extends Factory
{
    protected $model = Physician::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'specialty' => $this->faker->randomElement([
                'Cardiologia',
                'Clínica geral',
                'Dermatologia',
                'Endocrinologia',
                'Neurologia',
                'Ortopedia',
                'Pediatria',
                'Pneumologia',
            ]),
            'license_number' => 'DEMO-CRM-'.$this->faker->unique()->numerify('######'),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
        ];
    }
}
