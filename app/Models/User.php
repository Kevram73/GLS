<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'nom',
        'prenom',
        'num_phone',
        'type_user_id',
        'email',
        'password',
        'actif',
        'password_reset_token',
        'password_reset_expires_at',
        'otp_code',
        'otp_expires_at',
        'password_updated_at',
        'two_factor_enabled',
        'point_of_sale_id', // Un utilisateur peut avoir un point de vente (nullable)
        'is_commercial',
        'stock_journal',
        'image'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'password_reset_expires_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'password_updated_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'is_commercial' => 'boolean',
        'actif' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Relation : Un utilisateur peut avoir **au plus un** point de vente
     */
    public function pointOfSale()
    {
        return $this->hasOne(PointOfSale::class, 'manager_id');
    }

    /**
     * Scope : récupérer les utilisateurs **sans** point de vente
     */
    public function scopeWithoutPointOfSale($query)
    {
        return $query->whereNull('point_of_sale_id');
    }

    /**
     * Scope : récupérer les utilisateurs actifs **sans POS**
     */
    public function scopeActiveWithoutPointOfSale($query)
    {
        return $query->whereNull('point_of_sale_id')->where('actif', true);
    }
}
