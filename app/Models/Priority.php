<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'label',
        'rank',
        'color',
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
