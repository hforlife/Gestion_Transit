<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    //
    protected $fillable = [
        'nom',
        'email',
        'telephone',
        'adresse',
    ];

    public function colis()
    {
        return $this->hasMany(Colis::class);
    }

    public function dossierTransit()
    {
        return $this->belongTo(dossierTransit::class);
    }
}
