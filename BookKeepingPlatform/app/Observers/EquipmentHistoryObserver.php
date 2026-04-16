<?php

namespace App\Observers;

use App\Models\EquipmentHistory;

class EquipmentHistoryObserver
{
    /**
     * Handle the EquipmentHistory "created" event.
     */
    public function created(EquipmentHistory $equipmentHistory): void
    {
        //
    }

    /**
     * Handle the EquipmentHistory "updated" event.
     */
    public function updated(EquipmentHistory $equipmentHistory): void
    {
        //
    }

    /**
     * Handle the EquipmentHistory "deleted" event.
     */
    public function deleted(EquipmentHistory $equipmentHistory): void
    {
        //
    }

    /**
     * Handle the EquipmentHistory "restored" event.
     */
    public function restored(EquipmentHistory $equipmentHistory): void
    {
        //
    }

    /**
     * Handle the EquipmentHistory "force deleted" event.
     */
    public function forceDeleted(EquipmentHistory $equipmentHistory): void
    {
        //
    }
}
