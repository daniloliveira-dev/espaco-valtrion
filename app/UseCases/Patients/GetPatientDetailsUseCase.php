<?php

namespace App\UseCases\Patients;

use App\Interfaces\PatientRepositoryInterface;

final class GetPatientDetailsUseCase
{
    public function __construct(
        private readonly PatientRepositoryInterface $patients,
    ) {}

    /** @return array<string, mixed> */
    public function execute(int $patientId): array
    {
        return $this->patients->findDetailsById($patientId);
    }
}
