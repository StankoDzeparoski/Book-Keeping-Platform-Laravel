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

    /**
     * The attributes that are mass assignable.
     * Note: 'status' is intentionally excluded as it should only be set by observers
     * through Actions (Loan, Return, etc.)
     */
    protected $fillable = [
        'brand',
        'model',
        'category',
        'cost',
        'condition',
        'acquisition_date',
        'loan_date',
        'loan_expire_date',
        'storage_location',
        'user_id',
    ];

    protected $casts = [
        'category' => Category::class,
        'condition' => Condition::class,
        'status' => Status::class,
        'acquisition_date' => 'date',
        'loan_date' => 'date',
        'loan_expire_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(EquipmentHistory::class);
    }

    /**
     * Set the status attribute directly (for internal use by observers/actions)
     * This method bypasses the fillable protection
     */
    public function setStatus(Status $status): self
    {
        $this->attributes['status'] = $status;
        return $this;
    }
}
