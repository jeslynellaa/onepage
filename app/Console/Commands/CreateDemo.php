<?php

namespace App\Console\Commands;

use App\Services\DemoProvisioner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\URL;

class CreateDemo extends Command
{
    protected $signature = 'demo:create
        {--company= : Name shown for the demo company, e.g. "Acme Corp (Demo)"}
        {--hours=72 : How long the demo stays open}';

    protected $description = 'Provision a throwaway demo company with sample data and print its sign-in link.';

    public function handle(DemoProvisioner $provisioner): int
    {
        $hours = (int) $this->option('hours');

        if ($hours < 1) {
            $this->error('--hours must be at least 1.');

            return self::FAILURE;
        }

        $company = $provisioner->provision($this->option('company'), $hours);

        $link = URL::temporarySignedRoute('demo.enter', $company->demo_expires_at, ['company' => $company]);

        $this->info("Demo company \"{$company->name}\" is ready (expires {$company->demo_expires_at->toDayDateTimeString()}).");
        $this->line('Share this link — anyone with it can open the demo until it expires:');
        $this->newLine();
        $this->line($link);

        return self::SUCCESS;
    }
}
