<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PointOfSale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "name",
        "address",
        "city",
        "latitude",
        "longitude",
        "is_active",
    ];

    protected $dates = ['deleted_at'];

    /**
     * Scope a query to only include active points of sale.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the users associated with this point of sale.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
