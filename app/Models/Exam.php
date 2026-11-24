<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['patient_id', 'consultation_id', 'name', 'status', 'performed_at', 'result_summary'])]
class Exam extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'performed_at' => 'date',
        ];
    }
}
