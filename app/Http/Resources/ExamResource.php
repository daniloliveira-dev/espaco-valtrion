<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ExamResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => data_get($this->resource, 'id'),
            'name' => data_get($this->resource, 'name'),
            'status' => data_get($this->resource, 'status'),
            'performed_at' => data_get($this->resource, 'performed_at'),
            'result_summary' => data_get($this->resource, 'result_summary'),
            'documents' => ExamDocumentResource::collection(data_get($this->resource, 'documents', [])),
        ];
    }
}
