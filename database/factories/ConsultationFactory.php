<?php

namespace Database\Factories;

use App\Models\Consultation;
use App\Models\Patient;
use App\Models\Physician;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Consultation>
 */
class ConsultationFactory extends Factory
{
    protected $model = Consultation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $specialty = $this->faker->randomElement([
            'Cardiologia',
            'Clínica geral',
            'Dermatologia',
            'Endocrinologia',
            'Neurologia',
            'Ortopedia',
            'Pediatria',
            'Pneumologia',
        ]);
        $physicianName = $this->faker->name();

        return [
            'patient_id' => Patient::factory(),
            'physician_id' => Physician::factory()->state([
                'name' => $physicianName,
                'specialty' => $specialty,
            ]),
            'consultation_date' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'specialty' => $specialty,
            'physician_name' => $physicianName,
        ];
    }
}
