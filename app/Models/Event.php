<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'priority_id',
        'calendar_id',
        'title',
        'start_time',
        'end_time',
        'reservation_time',
        'status',
        'url',
        'detail',
        'reminder_sent'
    ];

    protected $casts = [
        'reminder_sent' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }
}
