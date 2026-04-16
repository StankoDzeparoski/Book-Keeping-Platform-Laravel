<?php

namespace App\Observers;

use App\Models\Equipment;
use App\Enums\Status;

class EquipmentObserver
{
    /**
     * Handle the Equipment "created" event.
     * Set initial status to AVAILABLE when equipment is first created
     */
    public function created(Equipment $equipment): void
    {
        // Set default status to AVAILABLE for newly created equipment
        if (!$equipment->status) {
            $equipment->forceFill(['status' => Status::AVAILABLE])->saveQuietly();
        }
    }

    /**
     * Handle the Equipment "updated" event.
     * Create or update equipment history when loan dates change
     */
    public function updated(Equipment $equipment): void
    {
        // Check if loan_date or loan_expire_date changed
        $loaned = $equipment->wasChanged(['loan_date', 'loan_expire_date', 'status']);

        if ($loaned && $equipment->user_id && $equipment->loan_date && $equipment->loan_expire_date) {
            // If equipment was just loaned (status changed to ASSIGNED), create/update history
            if ($equipment->status === Status::ASSIGNED) {
                $this->updateEquipmentHistory($equipment);
            }
        }
    }

    /**
     * Update the equipment history records
     */
    private function updateEquipmentHistory(Equipment $equipment): void
    {
        $history = $equipment->history()->latest('id')->first();

        if (!$history) {
            // Create new history record
            $equipment->history()->create([
                'user_ids' => [$equipment->user_id],
                'loan_date' => [$equipment->loan_date->format('Y-m-d')],
                'loan_expire_date' => [$equipment->loan_expire_date->format('Y-m-d')],
            ]);
        } else {
            // Update existing history - append new loan information
            $userIds = $history->user_ids ?? [];
            $loanDates = $history->loan_date ?? [];
            $loanExpireDates = $history->loan_expire_date ?? [];

            // Add current loan info if not already present
            if (!in_array($equipment->user_id, $userIds)) {
                $userIds[] = $equipment->user_id;
            }

            $loanDates[] = $equipment->loan_date->format('Y-m-d');
            $loanExpireDates[] = $equipment->loan_expire_date->format('Y-m-d');

            $history->update([
                'user_ids' => $userIds,
                'loan_date' => $loanDates,
                'loan_expire_date' => $loanExpireDates,
            ]);
        }
    }

    /**
     * Handle the Equipment "deleted" event.
     */
    public function deleted(Equipment $equipment): void
    {
        //
    }

    /**
     * Handle the Equipment "restored" event.
     */
    public function restored(Equipment $equipment): void
    {
        //
    }

    /**
     * Handle the Equipment "force deleted" event.
     */
    public function forceDeleted(Equipment $equipment): void
    {
        //
    }
}
