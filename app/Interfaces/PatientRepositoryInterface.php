<?php

namespace App\Interfaces;

use App\DTOs\Patients\PatientSearchData;
use Illuminate\Pagination\LengthAwarePaginator;

interface PatientRepositoryInterface
{
    public function search(PatientSearchData $filters): LengthAwarePaginator;

    /** @return array<string, mixed> */
    public function findDetailsById(int $patientId): array;
}
