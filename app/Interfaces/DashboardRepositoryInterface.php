<?php

namespace App\Interfaces;

interface DashboardRepositoryInterface
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
    public function getOverview(): array;
}
