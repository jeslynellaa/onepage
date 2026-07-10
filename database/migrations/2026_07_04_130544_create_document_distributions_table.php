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
        Schema::create('document_distributions', function (Blueprint $table) {$table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('received_at')->nullable();
            $table->timestamp('oriented_and_retrieved_at')->nullable();
            $table->timestamp('management_table_updated_at')->nullable();
            $table->foreignId('updated_by_controller_id')->nullable()->constrained('users'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_distributions');
    }
};
