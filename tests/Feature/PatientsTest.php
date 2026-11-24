<?php

namespace Tests\Feature;

use App\Models\Consultation;
use App\Models\Exam;
use App\Models\ExamDocument;
use App\Models\Patient;
use App\Models\Physician;
use App\Models\User;
use Database\Seeders\PatientSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PatientsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_users_can_filter_patients_by_name_exam_and_consultation_date(): void
    {
        $matchingPatient = Patient::query()->create([
            'medical_record_number' => 'VT-TEST-001',
            'name' => 'Ana Souza',
            'date_of_birth' => '1990-04-12',
            'email' => 'ana@example.test',
            'phone' => '11999990001',
        ]);
        $matchingConsultation = Consultation::query()->create([
            'patient_id' => $matchingPatient->id,
            'consultation_date' => '2026-03-08',
            'specialty' => 'Clínica geral',
            'physician_name' => 'Dra. Teste',
        ]);
        Exam::query()->create([
            'patient_id' => $matchingPatient->id,
            'consultation_id' => $matchingConsultation->id,
            'name' => 'Hemograma completo',
            'status' => 'completed',
            'performed_at' => '2026-03-08',
        ]);

        $otherPatient = Patient::query()->create([
            'medical_record_number' => 'VT-TEST-002',
            'name' => 'Bruno Lima',
            'date_of_birth' => '1985-09-20',
            'email' => 'bruno@example.test',
            'phone' => '11999990002',
        ]);
        $otherConsultation = Consultation::query()->create([
            'patient_id' => $otherPatient->id,
            'consultation_date' => '2026-03-09',
            'specialty' => 'Cardiologia',
            'physician_name' => 'Dr. Teste',
        ]);
        Exam::query()->create([
            'patient_id' => $otherPatient->id,
            'consultation_id' => $otherConsultation->id,
            'name' => 'Eletrocardiograma',
            'status' => 'completed',
            'performed_at' => '2026-03-09',
        ]);

        $this->actingAs(User::factory()->create())
            ->getJson('/api/patients?name=Ana&exam=Hemograma&date=2026-03-08')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.id', $matchingPatient->id)
            ->assertJsonPath('data.0.consultation_count', 1);
    }

    public function test_authenticated_users_can_view_patient_details_and_consultation_exams(): void
    {
        $patient = Patient::query()->create([
            'medical_record_number' => 'VT-TEST-003',
            'name' => 'Carla Mendes',
            'date_of_birth' => '1992-11-02',
            'email' => 'carla@example.test',
            'phone' => '11999990003',
        ]);
        $consultation = Consultation::query()->create([
            'patient_id' => $patient->id,
            'consultation_date' => '2026-04-14',
            'specialty' => 'Cardiologia',
            'physician_name' => 'Dra. Helena',
        ]);
        Exam::query()->create([
            'patient_id' => $patient->id,
            'consultation_id' => $consultation->id,
            'name' => 'Eletrocardiograma',
            'status' => 'completed',
            'performed_at' => '2026-04-14',
        ]);

        $this->actingAs(User::factory()->create())
            ->getJson("/api/patients/{$patient->id}")
            ->assertOk()
            ->assertJsonPath('data.name', 'Carla Mendes')
            ->assertJsonPath('data.consultations.0.consultation_date', '2026-04-14')
            ->assertJsonPath('data.consultations.0.exams.0.name', 'Eletrocardiograma');
    }

    public function test_seeded_patient_history_includes_physician_details_and_demo_exam_results(): void
    {
        Storage::fake('local');
        $this->seed(PatientSeeder::class);
        $patient = Patient::query()->where('medical_record_number', 'VT-0001')->firstOrFail();

        $this->actingAs(User::factory()->create())
            ->getJson("/api/patients/{$patient->id}")
            ->assertOk()
            ->assertJsonPath('data.consultations.0.physician.name', 'Dra. Helena Martins')
            ->assertJsonPath('data.consultations.0.physician.license_number', 'DEMO-CRM-0001')
            ->assertJsonPath('data.consultations.0.exams.0.result_summary', 'Resultado fictício de Eletrocardiograma para demonstração; sem valor diagnóstico.')
            ->assertJsonPath('data.consultations.0.exams.0.documents.0.title', 'Laudo médico');
    }

    public function test_guests_cannot_search_for_patients(): void
    {
        $this->getJson('/api/patients')->assertUnauthorized();
    }

    public function test_patient_seeder_is_idempotent(): void
    {
        Storage::fake('local');
        $this->seed(PatientSeeder::class);
        $initialCounts = [
            Patient::query()->count(),
            Physician::query()->count(),
            Consultation::query()->count(),
            Exam::query()->count(),
            Exam::query()->whereNotNull('result_summary')->count(),
            ExamDocument::query()->count(),
        ];

        $this->seed(PatientSeeder::class);

        $this->assertSame($initialCounts, [
            Patient::query()->count(),
            Physician::query()->count(),
            Consultation::query()->count(),
            Exam::query()->count(),
            Exam::query()->whereNotNull('result_summary')->count(),
            ExamDocument::query()->count(),
        ]);
    }
}
