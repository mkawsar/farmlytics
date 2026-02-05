<?php

namespace App\Models;

use App\Enums\Group;
use App\Enums\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Animal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'animal_id',
        'breed',
        'gender',
        'date_of_birth',
        'purchase_date',
        'purchase_price',
        'current_weight',
        'status',
        'grouping',
        'farm_id',
        'shed_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'current_weight' => 'decimal:2',
        'status' => Status::class,
        'grouping' => Group::class,
    ];

    public function farm(): BelongsTo
    {
        return $this->belongsTo(Farm::class);
    }

    public function shed(): BelongsTo
    {
        return $this->belongsTo(Shed::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
