<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentHistory extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'user_ids',
        'loan_date',
        'loan_expire_date',
    ];

    protected $casts = [
        'user_ids' => 'json',
        'loan_date' => 'json',
        'loan_expire_date' => 'json',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }
}
