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
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->boolean('acting_as_consultant')->default(false)->after('user_id');
            $table->foreignId('home_company_id')->nullable()->after('acting_as_consultant')->constrained('companies')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('home_company_id');
            $table->dropColumn('acting_as_consultant');
        });
    }
};
