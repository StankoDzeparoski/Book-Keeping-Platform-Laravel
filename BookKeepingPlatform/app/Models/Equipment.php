<?php

namespace App\Models;

use App\Enums\Category;
use App\Enums\Condition;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'brand',
        'model',
        'category',
        'cost',
        'condition',
        'status',
        'acquisition_date',
        'loan_date',
        'loan_expire_date',
        'storage_location',
        'employee_id',
    ];

    protected $casts = [
        'category' => Category::class,
        'condition' => Condition::class,
        'status' => Status::class,
        'acquisition_date' => 'date',
        'loan_date' => 'date',
        'loan_expire_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(EquipmentHistory::class);
    }
}


