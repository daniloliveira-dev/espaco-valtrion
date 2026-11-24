<?php

namespace App\UseCases\Patients;

use App\DTOs\Patients\PatientSearchData;
use App\Interfaces\PatientRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

final class SearchPatientsUseCase
{
    public function __construct(
        private readonly PatientRepositoryInterface $patients,
    ) {}

    public function execute(PatientSearchData $filters): LengthAwarePaginator
    {
        return $this->patients->search($filters);
    }
}
