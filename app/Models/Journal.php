<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Journal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "title",
        "price",
        "is_active",
    ];

    protected $dates = ['deleted_at']; 

    /**
     * Scope a query to only include active journals.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
