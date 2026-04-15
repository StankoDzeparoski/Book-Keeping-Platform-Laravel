<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRecord extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'equipment_id',
        'description',
        'cost',
        'maintenance_date',
    ];

    protected $casts = [
        'description' => 'json',
        'maintenance_date' => 'json',
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }
}
