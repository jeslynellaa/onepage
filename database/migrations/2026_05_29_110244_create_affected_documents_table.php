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
        Schema::create('affected_documents', function (Blueprint $table) {
            $table->id();
            // The main procedure document being created/saved
            $table->foreignId('parent_document_id')
                ->constrained('documents')
                ->onDelete('cascade');

            // The document record impacted from the dropdown select list
            $table->foreignId('affected_document_id')
                ->constrained('documents')
                ->onDelete('cascade');

            // Historical tracking parameters from your DIRF form layout
            $table->string('title');
            $table->string('code');
            $table->string('revision_number')->nullable();
            $table->text('details')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('affected_documents');
    }
};
