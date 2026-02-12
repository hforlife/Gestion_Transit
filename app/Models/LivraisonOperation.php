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
        return $this->belongsTo(Colis::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class);
    }
}
