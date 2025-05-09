<?php

namespace Modules\Prescriptions\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrescriptionItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'prescription_id',
        'name',
        'posology',
        'instructions',
        'is_supplement',
        'supplement_id',
        'dosage',
        'frequency',
        'duration',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_supplement' => 'boolean',
    ];

    /**
     * Get the prescription that owns the item.
     */
    public function prescription(): BelongsTo
    {
        return $this->belongsTo(Prescription::class);
    }

    /**
     * Get the supplement if this item references one.
     */
    public function supplement(): BelongsTo
    {
        // Assuming there's a Supplement model in a Settings module
        return $this->belongsTo(\Modules\Settings\Models\Supplement::class);
    }
}