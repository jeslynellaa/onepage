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
        Schema::create('dirfs', function (Blueprint $table) {
            $table->id();

            $table->string('dirf_no');
            $table->string('type_of_request');

            $table->string('type_of_documented_information')->nullable();
            $table->string('process')->nullable();
            $table->string('document_title')->nullable();
            
            $table->string('document_number')->nullable();
            $table->string('revision_number')->nullable();

            $table->text('justification')->nullable();
            $table->text('document_details')->nullable();

            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dirfs');
    }
};
