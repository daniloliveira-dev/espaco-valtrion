<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by_physician_id')->nullable()->constrained('physicians')->nullOnDelete();
            $table->string('document_type', 50);
            $table->string('title');
            $table->string('original_file_name');
            $table->string('storage_path')->unique();
            $table->string('mime_type', 127);
            $table->unsignedBigInteger('file_size');
            $table->text('description')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();
            $table->index(['exam_id', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_documents');
    }
};
