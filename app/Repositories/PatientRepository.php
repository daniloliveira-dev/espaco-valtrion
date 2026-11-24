<?php

namespace App\Repositories;

use App\DTOs\Patients\PatientSearchData;
use App\Interfaces\PatientRepositoryInterface;
use App\Models\Consultation;
use App\Models\Exam;
use App\Models\ExamDocument;
use App\Models\Patient;
use App\Models\Physician;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\LengthAwarePaginator;

final class PatientRepository implements PatientRepositoryInterface
{
    public function search(PatientSearchData $filters): LengthAwarePaginator
    {
        $query = Patient::query()
            ->select('patients.*')
            ->selectSub(
                Consultation::query()
                    ->selectRaw('count(*)')
                    ->whereColumn('consultations.patient_id', 'patients.id'),
                'consultation_count',
            )
            ->selectSub(
                Consultation::query()
                    ->select('consultation_date')
                    ->whereColumn('consultations.patient_id', 'patients.id')
                    ->orderByDesc('consultation_date')
                    ->orderByDesc('id')
                    ->limit(1),
                'latest_consultation_date',
            )
            ->selectSub(
                Consultation::query()
                    ->select('specialty')
                    ->whereColumn('consultations.patient_id', 'patients.id')
                    ->orderByDesc('consultation_date')
                    ->orderByDesc('id')
                    ->limit(1),
                'latest_consultation_specialty',
            )
            ->when(
                $filters->name,
                fn (Builder $query, string $name) => $query->where('patients.name', 'like', "%{$name}%"),
            );

        if ($filters->exam) {
            $query->whereExists(
                fn (QueryBuilder $query) => $query
                    ->selectRaw('1')
                    ->from('exams')
                    ->whereColumn('exams.patient_id', 'patients.id')
                    ->where('exams.name', 'like', "%{$filters->exam}%"),
            );
        }

        if ($filters->date) {
            $query->whereExists(
                fn (QueryBuilder $query) => $query
                    ->selectRaw('1')
                    ->from('consultations')
                    ->whereColumn('consultations.patient_id', 'patients.id')
                    ->whereDate('consultations.consultation_date', $filters->date),
            );
        }

        return $query
            ->orderBy('patients.name')
            ->paginate(10)
            ->withQueryString()
            ->through(static fn (Patient $patient): array => [
                'id' => $patient->id,
                'medical_record_number' => $patient->medical_record_number,
                'name' => $patient->name,
                'date_of_birth' => $patient->date_of_birth?->toDateString(),
                'email' => $patient->email,
                'phone' => $patient->phone,
                'consultation_count' => (int) $patient->consultation_count,
                'latest_consultation' => $patient->latest_consultation_date === null
                    ? null
                    : [
                        'consultation_date' => $patient->latest_consultation_date,
                        'specialty' => $patient->latest_consultation_specialty,
                    ],
            ]);
    }

    public function findDetailsById(int $patientId): array
    {
        $patient = Patient::query()->findOrFail($patientId);
        $consultations = Consultation::query()
            ->where('patient_id', $patientId)
            ->orderByDesc('consultation_date')
            ->orderByDesc('id')
            ->get();
        $consultationIds = $consultations->modelKeys();
        $examsByConsultation = Exam::query()
            ->where('patient_id', $patientId)
            ->whereIn('consultation_id', $consultationIds)
            ->orderBy('name')
            ->get()
            ->groupBy('consultation_id');
        $examIds = $examsByConsultation->flatten(1)->pluck('id');
        $documentsByExam = ExamDocument::query()
            ->whereIn('exam_id', $examIds)
            ->orderBy('id')
            ->get()
            ->groupBy('exam_id');
        $physicianIds = $consultations
            ->pluck('physician_id')
            ->merge($documentsByExam->flatten(1)->pluck('uploaded_by_physician_id'))
            ->filter()
            ->unique()
            ->values();
        $physicians = Physician::query()
            ->whereIn('id', $physicianIds)
            ->get()
            ->keyBy('id');

        return [
            'id' => $patient->id,
            'medical_record_number' => $patient->medical_record_number,
            'name' => $patient->name,
            'date_of_birth' => $patient->date_of_birth?->toDateString(),
            'email' => $patient->email,
            'phone' => $patient->phone,
            'consultations' => $consultations
                ->map(function (Consultation $consultation) use ($examsByConsultation, $documentsByExam, $physicians): array {
                    $physician = $physicians->get($consultation->physician_id);
                    $exams = $examsByConsultation->get($consultation->id, collect());

                    return [
                        'id' => $consultation->id,
                        'consultation_date' => $consultation->consultation_date?->toDateString(),
                        'specialty' => $consultation->specialty,
                        'physician_name' => $consultation->physician_name,
                        'physician' => $physician ? $this->physicianData($physician) : null,
                        'exams' => $exams->map(function (Exam $exam) use ($documentsByExam, $physicians): array {
                            $documents = $documentsByExam->get($exam->id, collect());

                            return [
                                'id' => $exam->id,
                                'name' => $exam->name,
                                'status' => $exam->status,
                                'performed_at' => $exam->performed_at?->toDateString(),
                                'result_summary' => $exam->result_summary,
                                'documents' => $documents->map(function (ExamDocument $document) use ($physicians): array {
                                    return [
                                        'id' => $document->id,
                                        'document_type' => $document->document_type,
                                        'title' => $document->title,
                                        'original_file_name' => $document->original_file_name,
                                        'mime_type' => $document->mime_type,
                                        'file_size' => $document->file_size,
                                        'description' => $document->description,
                                        'uploaded_at' => $document->uploaded_at?->toIso8601String(),
                                        'uploaded_by' => $physicians->get($document->uploaded_by_physician_id)?->name,
                                    ];
                                })->all(),
                            ];
                        })->all(),
                    ];
                })
                ->all(),
        ];
    }

    /**
     * @return array{id: int, name: string, specialty: string, license_number: string, email: ?string, phone: ?string}
     */
    private function physicianData(Physician $physician): array
    {
        return [
            'id' => $physician->id,
            'name' => $physician->name,
            'specialty' => $physician->specialty,
            'license_number' => $physician->license_number,
            'email' => $physician->email,
            'phone' => $physician->phone,
        ];
    }
}
