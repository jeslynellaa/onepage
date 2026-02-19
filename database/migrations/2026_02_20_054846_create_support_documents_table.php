<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('support_documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('code');
            $table->foreignId('section_id')->nullable();
            $table->string('revision_number');
            $table->date('effective_date')->nullable();
            $table->mediumText('objective');
            $table->mediumText('scope');
            $table->integer('pages')->nullable();
            $table->string('status')->nullable();
            $table->string('justification')->nullable();
            $table->string('file_path');

            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_documents');
    }
};
