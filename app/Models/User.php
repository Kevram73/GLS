<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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
        'point_of_sale_id',
        'is_commercial',
        'stock_journal'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'password_reset_expires_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'password_updated_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'is_commercial' => 'boolean',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Get the point of sale associated with the user.
     */
    public function pointOfSale()
    {
        return $this->belongsTo(PointOfSale::class);
    }

    /**
     * Get the user type.
     */
    public function userType()
    {
        return $this->belongsTo(UserType::class, 'type_user_id');
    }

    /**
     * Get all notifications for the user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get all messages sent by the user.
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get all message statuses for this user.
     */
    public function messageStatuses()
    {
        return $this->hasMany(MessageStatus::class, 'recipient_id');
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActive($query)
    {
        return $query->where('actif', true);
    }
}
