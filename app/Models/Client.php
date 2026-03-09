<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;
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
        return $this->hasOne(dossierTransit::class);
    }
}
