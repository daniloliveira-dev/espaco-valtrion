<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ExamDocumentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => data_get($this->resource, 'id'),
            'document_type' => data_get($this->resource, 'document_type'),
            'title' => data_get($this->resource, 'title'),
            'original_file_name' => data_get($this->resource, 'original_file_name'),
            'mime_type' => data_get($this->resource, 'mime_type'),
            'file_size' => data_get($this->resource, 'file_size'),
            'description' => data_get($this->resource, 'description'),
            'uploaded_at' => data_get($this->resource, 'uploaded_at'),
            'uploaded_by' => data_get($this->resource, 'uploaded_by'),
        ];
    }
}
