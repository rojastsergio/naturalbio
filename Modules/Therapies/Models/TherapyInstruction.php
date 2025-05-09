<?php

namespace Modules\Therapies\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TherapyInstruction extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'therapy_id',
        'doctor_id',
        'title',
        'description',
        'body_area',
        'instructions',
        'media',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'instructions' => 'array',
        'media' => 'array',
    ];

    /**
     * Get the therapy that owns the instruction.
     */
    public function therapy(): BelongsTo
    {
        return $this->belongsTo(Therapy::class);
    }

    /**
     * Get the doctor who created the instruction.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(\Modules\Doctors\Models\Doctor::class);
    }
}