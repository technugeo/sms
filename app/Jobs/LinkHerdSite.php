<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use TomatoPHP\FilamentTenancy\Models\Tenant;

class LinkHerdSite implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Tenant $tenant;

    public function __construct(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    /**
     * @return void
     */
    public function handle(): void
    {
        $domain = $this->tenant->domain;
        $command = "herd link {$domain}";
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            Log::error("Failed to link Herd site for tenant {$domain}: " . implode("\n", $output));
        } else {
            Log::info("Herd site linked successfully for tenant {$domain}");
        }
    }
}
