<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description'];

    public function votingLink()
    {
        return $this->hasOne(VotingLink::class);
    }

    public function agendaAnswers()
    {
        return $this->hasMany(AgendaAnswer::class);
    }

    public function publishedKeys()
    {
        return $this->hasMany(PublishedKey::class);
    }

    public function signature()
    {
        return $this->hasOne(Signature::class);
    }
}
