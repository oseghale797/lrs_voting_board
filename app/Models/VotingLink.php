<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VotingLink extends Model
{
    use HasFactory;

    protected $fillable = ['agenda_id', 'token'];

    public function agenda()
    {
        return $this->belongsTo(Agenda::class);
    }

    public function vote()
    {
        return $this->hasOne(Vote::class);
    }
}
