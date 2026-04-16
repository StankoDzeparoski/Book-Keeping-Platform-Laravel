<?php

namespace App\Actions;

use App\Models\Equipment;
use App\Models\User;
use App\Enums\Status;
use Carbon\Carbon;

class LoanEquipmentAction
{
    /**
     * Loan equipment to a user.
     *
     * @param Equipment $equipment The equipment to be loaned
     * @param User $user The user who is borrowing the equipment
     * @param string $loanDate The loan start date (YYYY-MM-DD format)
     * @param string $loanExpireDate The loan expiration date (YYYY-MM-DD format)
     * @return Equipment The updated equipment
     */
    public function execute(Equipment $equipment, User $user, string $loanDate, string $loanExpireDate): Equipment
    {
        // Update equipment with loan information
        $equipment->update([
            'user_id' => $user->id,
            'loan_date' => Carbon::createFromFormat('Y-m-d', $loanDate),
            'loan_expire_date' => Carbon::createFromFormat('Y-m-d', $loanExpireDate),
            'status' => Status::ASSIGNED,
        ]);

        return $equipment->refresh();
    }
}

