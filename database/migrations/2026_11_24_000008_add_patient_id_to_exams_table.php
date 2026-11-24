<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->foreignId('patient_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();
            $table->index(['patient_id', 'name']);
        });

        $exams = DB::table('exams')
            ->join('consultations', 'consultations.id', '=', 'exams.consultation_id')
            ->whereNull('exams.patient_id')
            ->orderBy('exams.id')
            ->get(['exams.id', 'consultations.patient_id']);

        foreach ($exams as $exam) {
            DB::table('exams')
                ->where('id', $exam->id)
                ->update(['patient_id' => $exam->patient_id]);
        }

        Schema::table('exams', function (Blueprint $table) {
            $table->unsignedBigInteger('patient_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropIndex(['patient_id', 'name']);
            $table->dropConstrainedForeignId('patient_id');
        });
    }
};
