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
        Schema::create('ms_manuals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('section_number');
            $table->string('revision_number')->nullable();
            $table->integer('pages')->nullable();
            $table->date('effective_date')->nullable();
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
        Schema::dropIfExists('ms_manuals');
    }
};
