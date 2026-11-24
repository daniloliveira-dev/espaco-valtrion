<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ConsultationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => data_get($this->resource, 'id'),
            'consultation_date' => data_get($this->resource, 'consultation_date'),
            'specialty' => data_get($this->resource, 'specialty'),
            'physician_name' => data_get($this->resource, 'physician_name'),
            'physician' => PhysicianResource::make(data_get($this->resource, 'physician')),
            'exams' => ExamResource::collection(data_get($this->resource, 'exams', [])),
        ];
    }
}
