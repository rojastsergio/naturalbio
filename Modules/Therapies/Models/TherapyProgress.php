<?php

namespace Modules\Therapies\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TherapyProgress extends Model
{
    use HasFactory;
    
    /**
     * Especificar el nombre de la tabla manualmente para usar singular en lugar de plural
     */
    protected $table = 'therapy_progress';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'therapy_assignment_id',
        'recorded_by',
        'recorded_at',
        'progress_percentage',
        'status',
        'notes',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'recorded_at' => 'datetime',
        'progress_percentage' => 'integer',
    ];
    
    /**
     * Get the therapy assignment that owns the progress.
     */
    public function therapyAssignment(): BelongsTo
    {
        return $this->belongsTo(TherapyAssignment::class);
    }
    
    /**
     * Get the user who recorded this progress.
     */
    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'recorded_by');
    }
}