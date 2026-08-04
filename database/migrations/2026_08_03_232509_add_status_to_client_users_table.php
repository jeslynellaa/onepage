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
        Schema::table('client_users', function (Blueprint $table) {
            $table->string('status')->default('active')->after('company_id');
            $table->timestamp('revoked_at')->nullable()->after('status');
            $table->foreignId('assigned_by_user_id')->nullable()->after('revoked_at')->constrained('users')->nullOnDelete();

            $table->index(['company_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_users', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'status']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropConstrainedForeignId('assigned_by_user_id');
            $table->dropColumn(['status', 'revoked_at']);
        });
    }
};
