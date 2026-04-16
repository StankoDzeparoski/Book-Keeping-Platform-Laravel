<?php

namespace App\Observers;

use App\Models\MaintenanceRecord;

class MaintenanceRecordObserver
{
    /**
     * Handle the MaintenanceRecord "created" event.
     */
    public function created(MaintenanceRecord $maintenanceRecord): void
    {
        //
    }

    /**
     * Handle the MaintenanceRecord "updated" event.
     */
    public function updated(MaintenanceRecord $maintenanceRecord): void
    {
        //
    }

    /**
     * Handle the MaintenanceRecord "deleted" event.
     */
    public function deleted(MaintenanceRecord $maintenanceRecord): void
    {
        //
    }

    /**
     * Handle the MaintenanceRecord "restored" event.
     */
    public function restored(MaintenanceRecord $maintenanceRecord): void
    {
        //
    }

    /**
     * Handle the MaintenanceRecord "force deleted" event.
     */
    public function forceDeleted(MaintenanceRecord $maintenanceRecord): void
    {
        //
    }
}
