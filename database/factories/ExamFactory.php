<?php

namespace Database\Factories;

use App\Models\Consultation;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Exam>
 */
class ExamFactory extends Factory
{
    protected $model = Exam::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['completed', 'scheduled', 'requested', 'cancelled']);
        $name = $this->faker->randomElement([
            'Eletrocardiograma',
            'Glicemia em jejum',
            'Hemograma completo',
            'Radiografia de tórax',
            'Ressonância magnética',
            'Ultrassonografia abdominal',
        ]);

        return [
            'consultation_id' => Consultation::factory(),
            'patient_id' => static function (array $attributes): int {
                return (int) Consultation::query()
                    ->findOrFail($attributes['consultation_id'])
                    ->patient_id;
            },
            'name' => $name,
            'status' => $status,
            'performed_at' => $status === 'completed'
                ? $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d')
                : null,
            'result_summary' => $status === 'completed'
                ? "Resultado fictício de {$name} para demonstração; sem valor diagnóstico."
                : null,
        ];
    }
}
