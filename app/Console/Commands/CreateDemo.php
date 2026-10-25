<?php

namespace App\Console\Commands;

use App\Services\DemoProvisioner;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CreateDemo extends Command
{
    protected $signature = 'demo:create
        {--company= : Name shown for the demo company, e.g. "Acme Corp (Demo)"}
        {--starts= : When the demo opens, e.g. "2026-11-02 09:00" (app timezone); defaults to now}
        {--hours=72 : How long the demo stays open, counted from when it opens}';

    protected $description = 'Provision a throwaway demo company with sample data and print its sign-in link.';

    public function handle(DemoProvisioner $provisioner): int
    {
        $hours = (int) $this->option('hours');

        if ($hours < 1) {
            $this->error('--hours must be at least 1.');

            return self::FAILURE;
        }

        $startsAt = null;
        if ($this->option('starts')) {
            try {
                $startsAt = Carbon::parse($this->option('starts'));
            } catch (\Throwable) {
                $this->error('--starts must be a date and time, e.g. "2026-11-02 09:00".');

                return self::FAILURE;
            }
        }

        $company = $provisioner->provision($this->option('company'), $hours, $startsAt);

        $this->info("Demo company \"{$company->name}\" is ready.");
        $this->line("Opens:   {$company->demo_starts_at->toDayDateTimeString()}");
        $this->line("Closes:  {$company->demo_expires_at->toDayDateTimeString()}");
        $this->line('Share this link — anyone with it can open the demo while it is open:');
        $this->newLine();
        $this->line(DemoProvisioner::linkFor($company));

        return self::SUCCESS;
    }
}
