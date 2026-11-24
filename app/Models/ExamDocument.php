<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'exam_id',
    'uploaded_by_physician_id',
    'document_type',
    'title',
    'original_file_name',
    'storage_path',
    'mime_type',
    'file_size',
    'description',
    'uploaded_at',
])]
class ExamDocument extends Model
{
    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'uploaded_at' => 'datetime',
        ];
    }
}
