<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Conversation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "participants"
    ];

    protected $dates = ['deleted_at']; // Ensure the deleted_at field is treated as a date
}
