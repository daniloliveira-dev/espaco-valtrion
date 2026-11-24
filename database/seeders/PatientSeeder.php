<?php

namespace Database\Seeders;

use App\Models\Consultation;
use App\Models\Exam;
use App\Models\Patient;
use App\Models\Physician;
use Illuminate\Database\Seeder;
use LogicException;

class PatientSeeder extends Seeder
{
    /**
     * The sample records are fictional and exist only for local development.
     */
    public function run(): void
    {
        $this->call(PhysicianSeeder::class);
        $physicians = Physician::query()->get();

        $records = [
            [
                'patient' => [
                    'medical_record_number' => 'VT-0001',
                    'name' => 'Ana Beatriz Costa',
                    'date_of_birth' => '1988-03-14',
                    'email' => 'ana.costa@example.test',
                    'phone' => '(11) 99999-1001',
                ],
                'consultations' => [
                    [
                        'consultation_date' => now()->subDays(3)->toDateString(),
                        'specialty' => 'Cardiologia',
                        'physician_name' => 'Dra. Helena Martins',
                        'exams' => [
                            ['name' => 'Eletrocardiograma', 'status' => 'completed', 'performed_at' => now()->subDays(3)->toDateString()],
                            ['name' => 'Hemograma completo', 'status' => 'completed', 'performed_at' => now()->subDays(2)->toDateString()],
                        ],
                    ],
                    [
                        'consultation_date' => now()->subDays(24)->toDateString(),
                        'specialty' => 'Clínica geral',
                        'physician_name' => 'Dr. Paulo Nascimento',
                        'exams' => [
                            ['name' => 'Glicemia em jejum', 'status' => 'completed', 'performed_at' => now()->subDays(23)->toDateString()],
                        ],
                    ],
                ],
            ],
            [
                'patient' => [
                    'medical_record_number' => 'VT-0002',
                    'name' => 'Carlos Eduardo Nunes',
                    'date_of_birth' => '1975-09-22',
                    'email' => 'carlos.nunes@example.test',
                    'phone' => '(21) 99999-2002',
                ],
                'consultations' => [
                    [
                        'consultation_date' => now()->subDays(6)->toDateString(),
                        'specialty' => 'Pneumologia',
                        'physician_name' => 'Dr. André Freitas',
                        'exams' => [
                            ['name' => 'Radiografia de tórax', 'status' => 'completed', 'performed_at' => now()->subDays(5)->toDateString()],
                        ],
                    ],
                ],
            ],
            [
                'patient' => [
                    'medical_record_number' => 'VT-0003',
                    'name' => 'Mariana Lopes Ribeiro',
                    'date_of_birth' => '1994-12-03',
                    'email' => 'mariana.ribeiro@example.test',
                    'phone' => '(31) 99999-3003',
                ],
                'consultations' => [
                    [
                        'consultation_date' => now()->subDay()->toDateString(),
                        'specialty' => 'Endocrinologia',
                        'physician_name' => 'Dra. Luiza Andrade',
                        'exams' => [
                            ['name' => 'Ultrassonografia abdominal', 'status' => 'scheduled', 'performed_at' => null],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($records as $record) {
            $patient = Patient::query()->updateOrCreate(
                ['medical_record_number' => $record['patient']['medical_record_number']],
                $record['patient'],
            );

            foreach ($record['consultations'] as $consultationData) {
                $exams = $consultationData['exams'];
                unset($consultationData['exams']);
                $physician = $physicians->firstWhere('name', $consultationData['physician_name']);

                if (! $physician instanceof Physician) {
                    throw new LogicException("No seeded physician matches {$consultationData['physician_name']}.");
                }

                $consultationData['physician_id'] = $physician->id;
                $consultation = Consultation::query()->updateOrCreate(
                    [
                        'patient_id' => $patient->id,
                        'specialty' => $consultationData['specialty'],
                    ],
                    $consultationData,
                );

                foreach ($exams as $examData) {
                    Exam::query()->updateOrCreate(
                        [
                            'consultation_id' => $consultation->id,
                            'name' => $examData['name'],
                        ],
                        [
                            'patient_id' => $patient->id,
                            'status' => $examData['status'],
                            'performed_at' => $examData['performed_at'],
                            'result_summary' => $examData['status'] === 'completed'
                                ? "Resultado fictício de {$examData['name']} para demonstração; sem valor diagnóstico."
                                : null,
                        ],
                    );
                }
            }
        }

        $specialties = ['Cardiologia', 'Clínica geral', 'Dermatologia', 'Endocrinologia', 'Neurologia', 'Ortopedia', 'Pediatria', 'Pneumologia'];
        $examNames = [
            'Eletrocardiograma',
            'Glicemia em jejum',
            'Hemograma completo',
            'Radiografia de tórax',
            'Ressonância magnética',
            'Ultrassonografia abdominal',
        ];

        for ($patientIndex = 0; $patientIndex < 30; $patientIndex++) {
            $generatedPatient = Patient::factory()->make();
            $patient = Patient::query()->updateOrCreate(
                ['medical_record_number' => sprintf('VT-%04d', $patientIndex + 100)],
                [
                    'name' => $generatedPatient->name,
                    'date_of_birth' => $generatedPatient->date_of_birth?->toDateString(),
                    'email' => $generatedPatient->email,
                    'phone' => $generatedPatient->phone,
                ],
            );
            $consultationCount = 1 + ($patientIndex % 4);

            for ($consultationIndex = 0; $consultationIndex < $consultationCount; $consultationIndex++) {
                $daysAgo = 5 + ($patientIndex * 2) + ($consultationIndex * 35);
                $consultationDate = now()->startOfDay()->subDays($daysAgo);
                $specialty = $specialties[($patientIndex + $consultationIndex) % count($specialties)];
                $physician = $physicians->firstWhere('specialty', $specialty);

                if (! $physician instanceof Physician) {
                    throw new LogicException("No seeded physician matches the {$specialty} specialty.");
                }

                $consultation = Consultation::query()->updateOrCreate(
                    [
                        'patient_id' => $patient->id,
                        'specialty' => $specialty,
                    ],
                    [
                        'consultation_date' => $consultationDate->toDateString(),
                        'physician_id' => $physician->id,
                        'physician_name' => $physician->name,
                    ],
                );
                $examCount = 1 + (($patientIndex + $consultationIndex) % 3);

                for ($examIndex = 0; $examIndex < $examCount; $examIndex++) {
                    $isCompleted = ($patientIndex + $consultationIndex + $examIndex) % 4 !== 0;
                    $examName = $examNames[($patientIndex + $consultationIndex + $examIndex) % count($examNames)];

                    Exam::query()->updateOrCreate(
                        [
                            'consultation_id' => $consultation->id,
                            'name' => $examName,
                        ],
                        [
                            'patient_id' => $patient->id,
                            'status' => $isCompleted ? 'completed' : 'scheduled',
                            'performed_at' => $isCompleted
                                ? $consultationDate->copy()->addDays($examIndex + 1)->toDateString()
                                : null,
                            'result_summary' => $isCompleted
                                ? "Resultado fictício de {$examName} para demonstração; sem valor diagnóstico."
                                : null,
                        ],
                    );
                }
            }
        }

        $this->call(ExamDocumentSeeder::class);
    }
}
