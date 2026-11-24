<?php

namespace Database\Seeders;

use App\Models\Physician;
use Illuminate\Database\Seeder;

class PhysicianSeeder extends Seeder
{
    /**
     * The professional records are fictional and exist only for local development.
     */
    public function run(): void
    {
        $physicians = [
            ['license_number' => 'DEMO-CRM-0001', 'name' => 'Dra. Helena Martins', 'specialty' => 'Cardiologia', 'email' => 'helena.martins@example.test', 'phone' => '(11) 0000-0001'],
            ['license_number' => 'DEMO-CRM-0002', 'name' => 'Dr. Paulo Nascimento', 'specialty' => 'Clínica geral', 'email' => 'paulo.nascimento@example.test', 'phone' => '(11) 0000-0002'],
            ['license_number' => 'DEMO-CRM-0003', 'name' => 'Dra. Sofia Almeida', 'specialty' => 'Dermatologia', 'email' => 'sofia.almeida@example.test', 'phone' => '(11) 0000-0003'],
            ['license_number' => 'DEMO-CRM-0004', 'name' => 'Dra. Luiza Andrade', 'specialty' => 'Endocrinologia', 'email' => 'luiza.andrade@example.test', 'phone' => '(11) 0000-0004'],
            ['license_number' => 'DEMO-CRM-0005', 'name' => 'Dr. Miguel Rocha', 'specialty' => 'Neurologia', 'email' => 'miguel.rocha@example.test', 'phone' => '(11) 0000-0005'],
            ['license_number' => 'DEMO-CRM-0006', 'name' => 'Dr. André Freitas', 'specialty' => 'Pneumologia', 'email' => 'andre.freitas@example.test', 'phone' => '(11) 0000-0006'],
            ['license_number' => 'DEMO-CRM-0007', 'name' => 'Dra. Camila Duarte', 'specialty' => 'Ortopedia', 'email' => 'camila.duarte@example.test', 'phone' => '(11) 0000-0007'],
            ['license_number' => 'DEMO-CRM-0008', 'name' => 'Dr. Rafael Moreira', 'specialty' => 'Pediatria', 'email' => 'rafael.moreira@example.test', 'phone' => '(11) 0000-0008'],
        ];

        foreach ($physicians as $physician) {
            Physician::query()->updateOrCreate(
                ['license_number' => $physician['license_number']],
                $physician,
            );
        }
    }
}
