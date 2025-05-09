<?php

namespace Modules\Supplements\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Prescriptions\Models\PrescriptionItem;

class Supplement extends Model
{
    use SoftDeletes;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array
     */
    protected $fillable = [
        'clinic_id',
        'name',
        'description',
        'instructions',
        'default_price',
        'price',
        'media',
        'status'
    ];

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'default_price' => 'decimal:2',
        'price' => 'decimal:2',
        'media' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Relación con los ítems de receta.
     */
    public function prescriptionItems()
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    /**
     * Scope: Suplementos activos.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Filtrar por clínica actual.
     */
    public function scopeForClinic($query)
    {
        return $query->where('clinic_id', session('clinic_id', 1));
    }
}