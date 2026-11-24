<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consultation_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('status', 32);
            $table->date('performed_at')->nullable();
            $table->timestamps();
            $table->index(['consultation_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
