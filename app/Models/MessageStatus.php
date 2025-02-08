<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageStatus extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "message_id",
        "recipient_id",
        "is_read",
        "read_at",
    ];

    protected $dates = ['deleted_at', 'read_at'];

    /**
     * Get the message associated with this status.
     */
    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    /**
     * Get the recipient of the message.
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
