<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublishedKey extends Model
{
    use HasFactory;
    protected $fillable = ['agenda_id', 'pk'];
    public function agenda()
    {
        return $this->belongsTo(Agenda::class);
    }
}
