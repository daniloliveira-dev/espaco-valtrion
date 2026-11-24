<?php

namespace Tests\Feature;

use App\Models\Consultation;
use App\Models\Exam;
use App\Models\ExamDocument;
use App\Models\Patient;
use App\Models\Physician;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExamDocumentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_users_can_view_an_exam_document_for_its_patient(): void
    {
        Storage::fake('local');
        [$patient, $exam, $document] = $this->createDocumentFixture();

        Storage::disk('local')->put($document->storage_path, 'Documento de teste.');

        $this->actingAs(User::factory()->create())
            ->get($this->contentUrl($patient, $exam, $document))
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_document_route_does_not_allow_a_different_patient_to_access_an_exam_file(): void
    {
        Storage::fake('local');
        [$patient, $exam, $document] = $this->createDocumentFixture();
        $otherPatient = Patient::query()->create([
            'medical_record_number' => 'VT-DOC-OTHER',
            'name' => 'Outro Paciente',
        ]);
        Storage::disk('local')->put($document->storage_path, 'Documento de teste.');

        $this->actingAs(User::factory()->create())
            ->get($this->contentUrl($otherPatient, $exam, $document))
            ->assertNotFound();
    }

    public function test_guests_cannot_view_exam_documents(): void
    {
        [$patient, $exam, $document] = $this->createDocumentFixture();

        $this->get($this->contentUrl($patient, $exam, $document))->assertUnauthorized();
    }

    /**
     * @return array{Patient, Exam, ExamDocument}
     */
    private function createDocumentFixture(): array
    {
        $patient = Patient::query()->create([
            'medical_record_number' => 'VT-DOC-001',
            'name' => 'Paciente de Teste',
        ]);
        $physician = Physician::factory()->create();
        $consultation = Consultation::query()->create([
            'patient_id' => $patient->id,
            'physician_id' => $physician->id,
            'consultation_date' => now()->toDateString(),
            'specialty' => $physician->specialty,
            'physician_name' => $physician->name,
        ]);
        $exam = Exam::query()->create([
            'patient_id' => $patient->id,
            'consultation_id' => $consultation->id,
            'name' => 'Exame de demonstração',
            'status' => 'completed',
            'performed_at' => now()->toDateString(),
        ]);
        $document = ExamDocument::query()->create([
            'exam_id' => $exam->id,
            'uploaded_by_physician_id' => $physician->id,
            'document_type' => 'medical_report',
            'title' => 'Laudo médico',
            'original_file_name' => 'laudo-demo.txt',
            'storage_path' => "test-documents/exam-{$exam->id}/laudo-demo.txt",
            'mime_type' => 'text/plain',
            'file_size' => 20,
            'description' => 'Arquivo de teste.',
        ]);

        return [$patient, $exam, $document];
    }

    private function contentUrl(Patient $patient, Exam $exam, ExamDocument $document): string
    {
        return "/api/patients/{$patient->id}/exams/{$exam->id}/documents/{$document->id}/content";
    }
}
