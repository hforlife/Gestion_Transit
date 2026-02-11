<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivraisonOperation extends Model
{
    protected $fillable = [
        'colis_id',
        'agent_id',
        'date_livraison',
        'statut'
    ];

    public function colis()
    {
        $this->belongTo(Colis::class);
    }

    public function agent()
    {
        $this->belongTo(User::class);
    }
}
