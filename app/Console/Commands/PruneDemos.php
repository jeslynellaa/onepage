<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PruneDemos extends Command
{
    protected $signature = 'demo:prune';

    protected $description = 'Delete expired demo companies, their data and their uploaded files.';

    public function handle(): int
    {
        $expired = Company::where('is_demo', true)
            ->where('demo_expires_at', '<=', now())
            ->get();

        foreach ($expired as $company) {
            $this->purge($company);
        }

        $this->info("Pruned {$expired->count()} expired demo compan".($expired->count() === 1 ? 'y' : 'ies').'.');

        return self::SUCCESS;
    }

    private function purge(Company $company): void
    {
        // Query builder throughout: no global scopes or model events, and nothing
        // here should depend on who (if anyone) is logged in.
        $files = collect([$company->logo_path])
            ->merge(DB::table('users')->where('company_id', $company->id)->pluck('signature_path'))
            ->merge(DB::table('ms_manuals')->where('company_id', $company->id)->pluck('file_path'))
            ->merge(DB::table('support_documents')->where('company_id', $company->id)->pluck('file_path'))
            ->merge(DB::table('forms')->where('company_id', $company->id)->pluck('file_path'))
            ->filter()
            ->all();

        DB::transaction(function () use ($company) {
            // document_distributions.updated_by_controller_id references users with no
            // ON DELETE action, so clear distributions before the company cascade
            // reaches the users table.
            $documentIds = DB::table('documents')->where('company_id', $company->id)->pluck('id');
            DB::table('document_distributions')->whereIn('document_id', $documentIds)->delete();

            // Every company-owned table cascades from companies.id.
            DB::table('companies')->where('id', $company->id)->delete();
        });

        Storage::disk('public')->delete($files);
    }
}
