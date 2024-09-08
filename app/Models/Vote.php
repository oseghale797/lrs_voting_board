<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'voting_link_id', 
        'public_key', 
        'agenda_answer_id',
        'linkability_tag'
    ];

    public function votingLink()
    {
        return $this->belongsTo(VotingLink::class);
    }

    public function agendaAnswer()
    {
        return $this->belongsTo(AgendaAnswer::class);
    }
}
