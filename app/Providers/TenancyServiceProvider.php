<?php

namespace App\Providers;

use App\Jobs\LinkHerdSite;
use Stancl\JobPipeline\JobPipeline;
use Stancl\Tenancy\Events\TenantCreated;
use TomatoPHP\FilamentTenancy\FilamentTenancyServiceProvider;

class TenancyServiceProvider extends FilamentTenancyServiceProvider
{
    //To make auto subdomain after client subscribe/register
//    public function databaseEvents(): array
//    {
//        $parentEvents = parent::databaseEvents();
//
//        // Merge the LinkHerdSite job into the existing TenantCreated pipeline
//        if (isset($parentEvents[TenantCreated::class])) {
//            $parentEvents[TenantCreated::class][0] = JobPipeline::make(
//                array_merge(
//                    $parentEvents[TenantCreated::class][0]->jobs, // Parent jobs
//                    [
//                        LinkHerdSite::class, // Add the new job
//                    ]
//                )
//            )->send(function (TenantCreated $event) {
//                return $event->tenant;
//            })->shouldBeQueued(true); // Set to `true` for production
//        }
//
//        return $parentEvents;
//    }
}
