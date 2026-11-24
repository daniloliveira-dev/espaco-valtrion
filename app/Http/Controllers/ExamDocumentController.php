<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamDocument;
use App\Models\Patient;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ExamDocumentController extends Controller
{
    public function content(Patient $patient, Exam $exam, int $document): StreamedResponse
    {
        $examDocument = $this->findPatientExamDocument($patient, $exam, $document);
        $allowedMimeTypes = ['application/pdf', 'image/jpeg', 'image/png', 'text/plain'];

        abort_unless(in_array($examDocument->mime_type, $allowedMimeTypes, true), 415);

        $storage = Storage::disk('local');
        $this->ensureDocumentExists($examDocument);

        return $storage->response(
            $examDocument->storage_path,
            $examDocument->original_file_name,
            [
                'Content-Type' => $examDocument->mime_type,
                'X-Content-Type-Options' => 'nosniff',
            ],
            'inline',
        );
    }

    public function download(Patient $patient, Exam $exam, int $document): StreamedResponse
    {
        $examDocument = $this->findPatientExamDocument($patient, $exam, $document);
        $storage = Storage::disk('local');
        $this->ensureDocumentExists($examDocument);

        return $storage->download(
            $examDocument->storage_path,
            $examDocument->original_file_name,
            [
                'Content-Type' => $examDocument->mime_type,
                'X-Content-Type-Options' => 'nosniff',
            ],
        );
    }

    private function findPatientExamDocument(Patient $patient, Exam $exam, int $document): ExamDocument
    {
        abort_unless(
            Exam::query()
                ->whereKey($exam->id)
                ->where('patient_id', $patient->id)
                ->exists(),
            404,
        );

        return ExamDocument::query()
            ->where('exam_id', $exam->id)
            ->findOrFail($document);
    }

    private function ensureDocumentExists(ExamDocument $document): void
    {
        abort_unless(
            Storage::disk('local')->exists($document->storage_path),
            404,
            'Documento não encontrado.',
        );
    }
}
