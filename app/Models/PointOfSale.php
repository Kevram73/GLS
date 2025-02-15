<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PointOfSale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'city',
        'latitude',
        'longitude',
        'is_active',
        'manager_id' // Un POS appartient à un **seul** utilisateur (manager)
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Scope pour récupérer uniquement les points de vente actifs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Relation : Un point de vente appartient à un **seul utilisateur** (manager)
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
