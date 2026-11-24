<?php

namespace App\UseCases\Dashboard;

final class GetDashboardUseCase
{
    /**
     * @return array{
     *     metrics: array<int, array{label: string, value: string, detail: string}>,
     *     appointments: array<int, array{time: string, patient: string, specialty: string, initials: string, status: string}>,
     *     weekly: array<int, array{day: string, visits: int, capacity: int}>
     * }
     */
    public function execute(): array
    {
        return [
            'metrics' => [
                ['label' => 'Pacientes ativos', 'value' => '1.284', 'detail' => '+8,2% este mês'],
                ['label' => 'Consultas hoje', 'value' => '48', 'detail' => '12 próximas'],
                ['label' => 'Profissionais', 'value' => '36', 'detail' => 'Em 8 especialidades'],
                ['label' => 'Leitos ocupados', 'value' => '72%', 'detail' => '18 disponíveis'],
            ],
            'appointments' => [
                ['time' => '09:00', 'patient' => 'Mariana Costa', 'specialty' => 'Cardiologia · Dra. Helena', 'initials' => 'MC', 'status' => 'confirmed'],
                ['time' => '09:30', 'patient' => 'Rafael Mendes', 'specialty' => 'Clínica geral · Dr. Paulo', 'initials' => 'RM', 'status' => 'confirmed'],
                ['time' => '10:15', 'patient' => 'Beatriz Almeida', 'specialty' => 'Pediatria · Dra. Luiza', 'initials' => 'BA', 'status' => 'pending'],
                ['time' => '11:00', 'patient' => 'João Carvalho', 'specialty' => 'Ortopedia · Dr. André', 'initials' => 'JC', 'status' => 'confirmed'],
            ],
            'weekly' => [
                ['day' => 'Seg', 'visits' => 28, 'capacity' => 39],
                ['day' => 'Ter', 'visits' => 37, 'capacity' => 43],
                ['day' => 'Qua', 'visits' => 31, 'capacity' => 40],
                ['day' => 'Qui', 'visits' => 44, 'capacity' => 49],
                ['day' => 'Sex', 'visits' => 35, 'capacity' => 41],
                ['day' => 'Sáb', 'visits' => 22, 'capacity' => 28],
                ['day' => 'Dom', 'visits' => 15, 'capacity' => 20],
            ],
        ];
    }
}
