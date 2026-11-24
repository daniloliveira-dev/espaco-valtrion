<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['patient_id', 'physician_id', 'consultation_date', 'specialty', 'physician_name'])]
class Consultation extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'consultation_date' => 'date',
        ];
    }
}
