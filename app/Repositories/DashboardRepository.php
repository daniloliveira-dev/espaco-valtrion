<?php

namespace App\Repositories;

use App\Interfaces\DashboardRepositoryInterface;
use App\Models\Exam;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;

final class DashboardRepository implements DashboardRepositoryInterface
{
    /**
     * @return array{
     *     metrics: array{total_patients: int, total_exams: int, pending_exams: int, completed_this_month: int},
     *     recent_exams: list<array{
     *         id: int,
     *         name: string,
     *         status: string,
     *         performed_at: ?string,
     *         consultation_date: string,
     *         specialty: string,
     *         patient: array{id: int, name: string, medical_record_number: string}
     *     }>
     * }
     */
    public function getOverview(): array
    {
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        $recentExams = DB::table('exams')
            ->join('consultations', 'consultations.id', '=', 'exams.consultation_id')
            ->join('patients', 'patients.id', '=', 'exams.patient_id')
            ->select([
                'exams.id',
                'exams.name',
                'exams.status',
                'exams.performed_at',
                'consultations.consultation_date',
                'consultations.specialty',
                'patients.id as patient_id',
                'patients.name as patient_name',
                'patients.medical_record_number',
            ])
            ->orderByDesc('consultations.consultation_date')
            ->orderByDesc('exams.id')
            ->limit(8)
            ->get()
            ->map(static fn (object $exam): array => [
                'id' => (int) $exam->id,
                'name' => $exam->name,
                'status' => $exam->status,
                'performed_at' => $exam->performed_at,
                'consultation_date' => $exam->consultation_date,
                'specialty' => $exam->specialty,
                'patient' => [
                    'id' => (int) $exam->patient_id,
                    'name' => $exam->patient_name,
                    'medical_record_number' => $exam->medical_record_number,
                ],
            ])
            ->all();

        return [
            'metrics' => [
                'total_patients' => Patient::query()->count(),
                'total_exams' => Exam::query()->count(),
                'pending_exams' => Exam::query()->whereIn('status', ['requested', 'scheduled'])->count(),
                'completed_this_month' => Exam::query()
                    ->where('status', 'completed')
                    ->whereBetween('performed_at', [$monthStart, $monthEnd])
                    ->count(),
            ],
            'recent_exams' => $recentExams,
        ];
    }
}
