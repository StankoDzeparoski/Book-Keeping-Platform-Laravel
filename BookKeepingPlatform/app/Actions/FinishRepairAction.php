<?php

namespace App\Actions;

use App\Models\Equipment;
use App\Enums\Status;
use App\Enums\Condition;

class FinishRepairAction
{
    /**
     * Finish repair on equipment.
     *
     * If equipment has no assigned user, status changes to AVAILABLE.
     * If equipment has an assigned user, status changes to ASSIGNED.
     * Equipment condition is updated to USED.
     *
     * @param Equipment $equipment The equipment to finish repair on
     * @return Equipment The updated equipment
     */
    public function execute(Equipment $equipment): Equipment
    {
        // Determine new status based on whether equipment is assigned
        $newStatus = $equipment->user_id ? Status::ASSIGNED : Status::AVAILABLE;

        // Update equipment status and condition
        $equipment->forceFill([
            'status' => $newStatus,
            'condition' => Condition::USED,
        ])->saveQuietly();

        return $equipment->refresh();
    }
}

