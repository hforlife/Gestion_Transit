<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DossierTransit extends Model
{
    //
    protected $fillable = [
        'colis_id',
        'agent_id',
        'id_type_dossier',
        'nom',
        'reference',
        'date_depot',
        'statut'
    ];

    public function colis()
    {
        return $this->belongsTo(Colis::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'id_dossier_transit');
    }

    public function type_dossier()
    {
        return $this->belongsTo(TypeDossier::class, 'id_type_dossier');
    }

    public function trackingEvents()
    {
        return $this->morphMany(TrackingEvent::class, 'trackable')
            ->orderBy('created_at');
    }


}
