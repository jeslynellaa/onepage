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
        Schema::table('companies', function (Blueprint $table) {
            $table->timestamp('demo_starts_at')->nullable()->after('is_demo');
        });

        // Book Demo submissions, kept so a scheduled demo can be created from one.
        // Platform-level (not tenant-scoped): only the host admin sees these.
        Schema::create('demo_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('company');
            $table->string('team_size');
            $table->date('demo_date');
            $table->string('demo_time');
            $table->foreignId('demo_company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demo_requests');

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('demo_starts_at');
        });
    }
};
