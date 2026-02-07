<?php

namespace App\Models;

use App\Enums\IncomeType;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncomeTransaction extends Model
{
    protected $fillable = [
        'farm_id',
        'shed_id',
        'animal_id',
        'income_type',
        'amount',
        'quantity_liter',
        'rate_per_liter',
        'transaction_date',
        'buyer',
        'payment_status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'quantity_liter' => 'decimal:2',
        'rate_per_liter' => 'decimal:2',
        'transaction_date' => 'date',
        'income_type' => IncomeType::class,
        'payment_status' => PaymentStatus::class,
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function shed(): BelongsTo
    {
        return $this->belongsTo(Shed::class);
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
