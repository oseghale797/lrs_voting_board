<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Signature extends Model
{
    use HasFactory;
    protected $fillable = [
        'agenda_id',
        'linkability_tag',
        'signature',
        'message',
        'pk_signer'
    ];

    public function agenda()
    {
        return $this->belongsTo(Agenda::class);
    }
}
