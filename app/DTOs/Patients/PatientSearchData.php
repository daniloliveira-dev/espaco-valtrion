<?php

namespace App\DTOs\Patients;

final readonly class PatientSearchData
{
    public function __construct(
        public ?string $name,
        public ?string $exam,
        public ?string $date,
    ) {}

    /**
     * @param array{name?: ?string, exam?: ?string, date?: ?string} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: isset($data['name']) ? trim($data['name']) : null,
            exam: isset($data['exam']) ? trim($data['exam']) : null,
            date: $data['date'] ?? null,
        );
    }
}
