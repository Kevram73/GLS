<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vente extends Model
{
    use HasFactory, SoftDeletes;

    
    protected $fillable = [
        "date",
        "montant",
        "point_of_sale_id",
        "client_id",
        "journal_id",
        "nbre",
        "seller_id",
        "is_paid",
    ];

    protected $dates = ['deleted_at', 'date'];

    protected $casts = [
        'is_paid' => 'boolean',
    ];

    /**
     * Get the point of sale where the sale occurred.
     */
    public function pointOfSale()
    {
        return $this->belongsTo(PointOfSale::class);
    }

    /**
     * Get the client who made the purchase.
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the journal associated with the sale.
     */
    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    /**
     * Get the seller who made the sale.
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Scope a query to only include paid sales.
     */
    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    /**
     * Scope a query to only include unpaid sales.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    /**
     * Scope a query to get sales by a specific seller.
     */
    public function scopeBySeller($query, $sellerId)
    {
        return $query->where('seller_id', $sellerId);
    }

    /**
     * Scope a query to get sales by a specific point of sale.
     */
    public function scopeByPointOfSale($query, $pointOfSaleId)
    {
        return $query->where('point_of_sale_id', $pointOfSaleId);
    }
}
