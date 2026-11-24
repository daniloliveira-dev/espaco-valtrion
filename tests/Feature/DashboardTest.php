<?php

namespace Tests\Feature;

use App\Models\Consultation;
use App\Models\Exam;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_dashboard_shows_patient_and_exam_data_from_the_database(): void
    {
        $this->travelTo(now()->startOfMonth()->addDays(10));

        $patient = Patient::query()->create([
            'medical_record_number' => 'VT-DASH-001',
            'name' => 'Ana Souza',
            'email' => 'ana@example.test',
            'phone' => '11999990001',
        ]);
        $consultation = Consultation::query()->create([
            'patient_id' => $patient->id,
            'consultation_date' => now()->toDateString(),
            'specialty' => 'Cardiologia',
            'physician_name' => 'Dra. Teste',
        ]);
        Exam::query()->create([
            'patient_id' => $patient->id,
            'consultation_id' => $consultation->id,
            'name' => 'Hemograma completo',
            'status' => 'completed',
            'performed_at' => now()->toDateString(),
        ]);
        Exam::query()->create([
            'patient_id' => $patient->id,
            'consultation_id' => $consultation->id,
            'name' => 'Eletrocardiograma',
            'status' => 'scheduled',
            'performed_at' => null,
        ]);

        $this->actingAs(User::factory()->create())
            ->getJson('/api/dashboard')
            ->assertOk()
            ->assertJsonPath('metrics.total_patients', 1)
            ->assertJsonPath('metrics.total_exams', 2)
            ->assertJsonPath('metrics.pending_exams', 1)
            ->assertJsonPath('metrics.completed_this_month', 1)
            ->assertJsonPath('recent_exams.0.patient.name', 'Ana Souza')
            ->assertJsonPath('recent_exams.0.name', 'Eletrocardiograma')
            ->assertJsonMissingPath('recent_exams.0.patient.email');
    }

    public function test_guests_cannot_access_patient_dashboard_data(): void
    {
        $this->getJson('/api/dashboard')->assertUnauthorized();
    }
}
