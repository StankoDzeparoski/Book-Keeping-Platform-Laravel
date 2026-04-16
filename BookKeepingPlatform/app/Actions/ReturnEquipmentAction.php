<?php

namespace App\Actions;

use App\Models\Equipment;
use App\Enums\Status;
use Carbon\Carbon;

class ReturnEquipmentAction
{
    /**
     * Return equipment from a user.
     *
     * @param Equipment $equipment The equipment being returned
     * @param string $returnDate The return date (YYYY-MM-DD format)
     * @return Equipment The updated equipment
     */
    public function execute(Equipment $equipment, string $returnDate): Equipment
    {
        // Update equipment - clear loan info and set status to Available
        $equipment->update([
            'loan_expire_date' => Carbon::createFromFormat('Y-m-d', $returnDate),
            'status' => Status::AVAILABLE,
        ]);

        return $equipment->refresh();
    }
}

