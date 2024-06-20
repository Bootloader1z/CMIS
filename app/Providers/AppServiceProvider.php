<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\TasFileObserver;
use App\Models\TasFile;
use App\Models\Admitted;
use App\Observers\AdmittedObserver;
use App\Models\ApprehendingOfficer;
use App\Observers\ApprehendingOfficerObserver;
use App\Models\TrafficViolation;
use App\Observers\TrafficViolationObserver;
use App\Models\Department;
use App\Observers\DepartmentObserver;
use App\Models\G5ChatMessage;
use App\Observers\G5ChatMessageObserver;
use App\Models\Archives;
use App\Observers\ArchivesObserver;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        TasFile::observe(TasFileObserver::class);
        Admitted::observe(AdmittedObserver::class);
        ApprehendingOfficer::observe(ApprehendingOfficerObserver::class);
        TrafficViolation::observe(TrafficViolationObserver::class);
        Department::observe(DepartmentObserver::class);
        G5ChatMessage::observe(G5ChatMessageObserver::class);
        Archives::observe(ArchivesObserver::class);
    }
}
