<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'department_id',
    ];

    /**
     * Get the department that owns the municipality.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the clinics associated with the municipality.
     */
    public function clinics()
    {
        return $this->hasMany(Clinic::class);
    }
}