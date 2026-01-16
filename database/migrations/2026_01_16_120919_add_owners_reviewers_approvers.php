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
        Schema::table('sections', function (Blueprint $table) {
            // Add the foreign key columns
            $table->unsignedBigInteger('process_owner_id')->nullable();
            $table->unsignedBigInteger('reviewer_id')->nullable();
            $table->unsignedBigInteger('approver_id')->nullable();

            // Set them as foreign keys referencing users table
            $table->foreign('process_owner_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('approver_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['process_owner_id']);
            $table->dropForeign(['reviewer_id']);
            $table->dropForeign(['approver_id']);

            // Drop columns
            $table->dropColumn(['process_owner_id', 'reviewer_id', 'approver_id']);
        });
    }
};
