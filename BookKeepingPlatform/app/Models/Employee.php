<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'name',
        'surname',
        'dob',
        'equipment_ids',
    ];

    protected $casts = [
        'equipment_ids' => 'json',
    ];

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class);
    }

    public function equipmentHistories(): HasMany
    {
        return $this->hasMany(EquipmentHistory::class);
    }
}
