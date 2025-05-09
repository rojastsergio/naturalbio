<?php

namespace Modules\Recommendations\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Modules\Patients\Models\Patient;
use Modules\Supplements\Models\Supplement;
use Modules\Therapies\Models\Therapy;

class PatientRecommendation extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'patient_id',
        'created_by',
        'title',
        'type',
        'reference_id',
        'tasks',
        'accepted',
        'due_date',
        'progress',
        'notes',
        'status',
    ];

    /**
     * Los atributos que deben ser convertidos.
     *
     * @var array
     */
    protected $casts = [
        'tasks' => 'array',
        'accepted' => 'boolean',
        'due_date' => 'date',
        'progress' => 'integer',
    ];

    /**
     * Relación con el paciente.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Relación con el usuario que creó la recomendación.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relación con el suplemento.
     * Esta relación se carga independientemente del tipo, pero solo
     * devolverá resultados si el tipo es 'supplement' y el reference_id coincide.
     */
    public function supplement()
    {
        return $this->belongsTo(Supplement::class, 'reference_id');
    }

    /**
     * Relación con la terapia.
     * Esta relación se carga independientemente del tipo, pero solo
     * devolverá resultados si el tipo es 'therapy' y el reference_id coincide.
     */
    public function therapy()
    {
        return $this->belongsTo(Therapy::class, 'reference_id');
    }

    /**
     * Scope para filtrar recomendaciones activas.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope para filtrar recomendaciones completadas.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope para filtrar recomendaciones pendientes.
     */
    public function scopePending($query)
    {
        return $query->where('accepted', false)->where('status', 'active');
    }

    /**
     * Calcula el progreso basado en las tareas completadas.
     */
    public function calculateProgress()
    {
        if (!$this->tasks || empty($this->tasks)) {
            return 0;
        }

        $total = count($this->tasks);
        $completed = 0;

        foreach ($this->tasks as $task) {
            if (isset($task['completed']) && $task['completed']) {
                $completed++;
            }
        }

        return $total > 0 ? round(($completed / $total) * 100) : 0;
    }

    /**
     * Actualiza el progreso en la base de datos.
     */
    public function updateProgress()
    {
        $this->progress = $this->calculateProgress();
        $this->save();

        // Si todas las tareas están completadas, marcar como completada
        if ($this->progress === 100) {
            $this->status = 'completed';
            $this->save();
        }

        return $this->progress;
    }
}