<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class PatientResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => data_get($this->resource, 'id'),
            'medical_record_number' => data_get($this->resource, 'medical_record_number'),
            'name' => data_get($this->resource, 'name'),
            'date_of_birth' => data_get($this->resource, 'date_of_birth'),
            'email' => data_get($this->resource, 'email'),
            'phone' => data_get($this->resource, 'phone'),
            'consultation_count' => $this->when(
                array_key_exists('consultation_count', $this->resource),
                fn () => data_get($this->resource, 'consultation_count'),
            ),
            'latest_consultation' => $this->when(
                array_key_exists('latest_consultation', $this->resource),
                fn () => data_get($this->resource, 'latest_consultation'),
            ),
            'consultations' => $this->when(
                array_key_exists('consultations', $this->resource),
                fn () => ConsultationResource::collection(data_get($this->resource, 'consultations')),
            ),
        ];
    }
}
