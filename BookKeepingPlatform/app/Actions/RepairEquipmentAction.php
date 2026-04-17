<?php

namespace App\Actions;

use App\Models\Equipment;
use App\Models\MaintenanceRecord;
use App\Enums\Status;
use Carbon\Carbon;

class RepairEquipmentAction
{
    /**
     * Create or update a maintenance record for equipment repair.
     * Also changes the equipment status to REPAIR.
     *
     * If no maintenance record exists for the equipment, create a new one.
     * If one exists, append the new maintenance date and description to the arrays,
     * and increment the cost by the provided amount.
     *
     * @param Equipment $equipment The equipment being repaired
     * @param string $description The description of the repair
     * @param int $cost The cost of the repair
     * @param string $maintenanceDate The maintenance date (YYYY-MM-DD format)
     * @return MaintenanceRecord The created or updated maintenance record
     */
    public function execute(Equipment $equipment, string $description, int $cost, string $maintenanceDate): MaintenanceRecord
    {
        $maintenanceDateCarbon = Carbon::createFromFormat('Y-m-d', $maintenanceDate);

        // Change equipment status to REPAIR
        $equipment->forceFill([
            'status' => Status::REPAIR,
        ])->saveQuietly();

        // Get the latest maintenance record for this equipment
        $maintenanceRecord = MaintenanceRecord::where('equipment_id', $equipment->id)
            ->latest('id')
            ->first();

        if (!$maintenanceRecord) {
            // Create new maintenance record
            $maintenanceRecord = MaintenanceRecord::create([
                'equipment_id' => $equipment->id,
                'description' => [$description],
                'maintenance_date' => [$maintenanceDateCarbon->format('Y-m-d')],
                'cost' => $cost,
            ]);
        } else {
            // Get current values - they're stored as JSON arrays
            $descriptions = $maintenanceRecord->description ?? [];
            $maintenanceDates = $maintenanceRecord->maintenance_date ?? [];

            // Ensure they're arrays
            if (!is_array($descriptions)) {
                $descriptions = [];
            }
            if (!is_array($maintenanceDates)) {
                $maintenanceDates = [];
            }

            // Append new maintenance information
            $descriptions[] = $description;
            $maintenanceDates[] = $maintenanceDateCarbon->format('Y-m-d');

            // Increment cost by the provided amount
            $newCost = $maintenanceRecord->cost + $cost;

            // Update the maintenance record
            $maintenanceRecord->forceFill([
                'description' => $descriptions,
                'maintenance_date' => $maintenanceDates,
                'cost' => $newCost,
            ])->saveQuietly();

            $maintenanceRecord = $maintenanceRecord->refresh();
        }

        return $maintenanceRecord;
    }
}


