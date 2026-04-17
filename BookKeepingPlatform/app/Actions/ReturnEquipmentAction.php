<?php

namespace App\Actions;

use App\Models\Equipment;
use App\Models\EquipmentHistory;
use App\Enums\Status;
use Carbon\Carbon;

class ReturnEquipmentAction
{
    /**
     * Return equipment from a user.
     * Also updates the EquipmentHistory to reflect the actual return date.
     *
     * Equipment only becomes AVAILABLE if the return date is today.
     * If the return date is in the future, the equipment remains ASSIGNED but the
     * loan_expire_date is updated to the new return date.
     *
     * @param Equipment $equipment The equipment being returned
     * @param string $returnDate The return date (YYYY-MM-DD format)
     * @return Equipment The updated equipment
     */
    public function execute(Equipment $equipment, string $returnDate): Equipment
    {
        // Store equipment ID before modifications
        $equipmentId = $equipment->id;
        $returnDateCarbon = Carbon::createFromFormat('Y-m-d', $returnDate);
        $today = Carbon::now()->startOfDay();
        $returnDateCarbon = $returnDateCarbon->startOfDay();

        // Determine if equipment should become available immediately
        $isReturnToday = $returnDateCarbon->equalTo($today);

        // Update equipment based on whether this is today's return or future return
        $updateData = [
            'loan_expire_date' => $returnDateCarbon,
        ];

        if ($isReturnToday) {
            // Only clear loan info and set to AVAILABLE if returning today
            $updateData['loan_date'] = null;
            $updateData['user_id'] = null;
            $updateData['status'] = Status::AVAILABLE;
        }
        // For future returns, keep the equipment ASSIGNED - don't clear user_id or loan_date

        // Use saveQuietly() to prevent observer from creating duplicate history entries
        // We manually handle history updates in this action
        $equipment->forceFill($updateData)->saveQuietly();

        // Refresh equipment from database to ensure relationship cache is cleared
        $equipment = Equipment::find($equipmentId);

        // Update the equipment history with the actual return date
        // This will only update the last expiration date, not create new entries
        $this->updateEquipmentHistory($equipment, $returnDate);

        // Final refresh to ensure we return the fully updated equipment with all changes
        return $equipment->refresh();
    }

    /**
     * Update the equipment history to reflect the actual return date.
     * Always updates the last entry's expiration date to the actual return date.
     */
    private function updateEquipmentHistory(Equipment $equipment, string $returnDate): void
    {
        // Query directly from database to avoid relationship caching issues
        // Get the latest history record for this equipment
        $history = EquipmentHistory::where('equipment_id', $equipment->id)
            ->latest('id')
            ->first();

        if (!$history) {
            return; // No history to update
        }

        // Get the current loan_expire_date - it's stored as a JSON array
        $loanExpireDates = $history->loan_expire_date;

        // Ensure it's an array
        if (!is_array($loanExpireDates)) {
            $loanExpireDates = [];
        }

        if (count($loanExpireDates) === 0) {
            return; // No loan dates to update
        }

        // Always update the last expiration date with the actual return date
        // This records when the equipment was actually returned, regardless of early/on-time/late
        $loanExpireDates[count($loanExpireDates) - 1] = $returnDate;

        // Use forceFill to properly update the JSON attribute
        $history->forceFill([
            'loan_expire_date' => $loanExpireDates,
        ])->save(); // Use save() instead of saveQuietly() to ensure it persists
    }
}


