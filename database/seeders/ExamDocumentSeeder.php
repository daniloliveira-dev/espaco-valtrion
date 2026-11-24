<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\ExamDocument;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use LogicException;
use RuntimeException;

class ExamDocumentSeeder extends Seeder
{
    /**
     * The generated files are fictional text documents for local development only.
     */
    public function run(): void
    {
        $demoMedicalRecordNumbers = array_merge(
            ['VT-0001', 'VT-0002', 'VT-0003'],
            array_map(static fn (int $number): string => sprintf('VT-%04d', $number), range(100, 129)),
        );

        $exams = Exam::query()
            ->join('consultations', 'consultations.id', '=', 'exams.consultation_id')
            ->join('patients', 'patients.id', '=', 'exams.patient_id')
            ->leftJoin('physicians', 'physicians.id', '=', 'consultations.physician_id')
            ->where('exams.status', 'completed')
            ->whereIn('patients.medical_record_number', $demoMedicalRecordNumbers)
            ->select([
                'exams.*',
                'patients.name as demo_patient_name',
                'patients.medical_record_number as demo_medical_record_number',
                'consultations.specialty as demo_specialty',
                'physicians.id as demo_physician_id',
                'physicians.name as demo_physician_name',
            ])
            ->get();

        foreach ($exams as $exam) {
            if ($exam->demo_physician_id === null) {
                throw new LogicException("Completed demo exam {$exam->id} has no physician.");
            }

            $documents = [
                ['type' => 'medical_report', 'title' => 'Laudo médico'],
            ];

            if ($exam->id % 3 === 0) {
                $documents[] = ['type' => 'medical_certificate', 'title' => 'Atestado médico'];
            }

            if ($exam->id % 5 === 0) {
                $documents[] = ['type' => 'other', 'title' => 'Documento complementar'];
            }

            foreach ($documents as $documentData) {
                $path = "patient-documents/demo/exam-{$exam->id}/{$documentData['type']}.txt";
                $fileName = "{$documentData['type']}-exam-{$exam->id}-demo.txt";
                $contents = implode(PHP_EOL, [
                    'DOCUMENTO FICTÍCIO PARA DEMONSTRAÇÃO',
                    'Este conteúdo não é um documento clínico ou legal e não deve orientar decisões.',
                    '',
                    "Tipo: {$documentData['title']}",
                    "Paciente: {$exam->demo_patient_name}",
                    "Prontuário: {$exam->demo_medical_record_number}",
                    "Exame: {$exam->name}",
                    "Médico: {$exam->demo_physician_name}",
                    "Especialidade: {$exam->demo_specialty}",
                    'Resultado clínico: não informado neste arquivo de demonstração.',
                ]);
                $document = ExamDocument::query()->updateOrCreate(
                    ['storage_path' => $path],
                    [
                        'exam_id' => $exam->id,
                        'uploaded_by_physician_id' => $exam->demo_physician_id,
                        'document_type' => $documentData['type'],
                        'title' => $documentData['title'],
                        'original_file_name' => $fileName,
                        'mime_type' => 'text/plain',
                        'file_size' => strlen($contents),
                        'description' => 'Arquivo fictício de demonstração.',
                        'uploaded_at' => $exam->performed_at ?? now(),
                    ],
                );

                if (! Storage::disk('local')->put($document->storage_path, $contents)) {
                    throw new RuntimeException("Unable to write demo document {$document->storage_path}.");
                }
            }
        }
    }
}
