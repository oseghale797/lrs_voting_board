<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgendaAnswer extends Model
{
    use HasFactory;
    protected $fillable = ['agenda_id', 'answer'];

    public function agenda()
    {
        return $this->belongsTo(Agenda::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
