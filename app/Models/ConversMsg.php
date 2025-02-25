<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversMsg extends Model
{
    use HasFactory;

    protected $fillable = [
        "content",
        "file",
        "date_sent",
        "time_sent",
        "send_time",
        "sender_id",
        "receiver_id"
    ];
}
