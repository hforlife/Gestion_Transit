<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DossierTransit extends Model
{
    //
    protected $fillable = [
        'client_id',
        'agent_id',
        'id_type_dossier',
        'nom',
        'reference',
        'date_depot',
        'statut'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'id_dossier_transit');
    }

    public function type_dossier()
    {
        return $this->belongsTo(TypeDossier::class, 'id_type_dossier');
    }

    public function colis()
    {
        return $this->hasOne(Colis::class, 'id_dossier_transit');
    }

}
