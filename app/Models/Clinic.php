<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'logo',
        'status',
        'municipality_id',
    ];

    /**
     * Get the users associated with the clinic.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the municipality associated with the clinic.
     */
    public function municipality()
    {
        return $this->belongsTo(Municipality::class);
    }
}