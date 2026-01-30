<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Colis extends Model
{
    //
    protected $fillable = [
        'numero_bl',
        'description',
        'etat_colis',
        'id_type_colis',
        'user_id',
        'id_port',
        'client_id'
    ];

    public function typeColis()
    {
        return $this->belongsTo(TypeColis::class, 'id_type_colis');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function port()
    {
        return $this->belongsTo(Port::class, 'id_port');
    }

    public function dossierTransit()
    {
        return $this->hasOne(DossierTransit::class);
    }

    public function portOperation()
    {
        return $this->hasOne(PortOperation::class);
    }

    public function douaneOperation()
    {
        return $this->hasOne(DouaneOperation::class);
    }

    public function expertise()
    {
        return $this->hasOne(Expertise::class);
    }

    public function changeStatus($status)
    {
        $this->etat_colis = $status;
        $this->save();
    }

    /**
     * Historique de tracking du colis
     */
    public function trackingEvents()
    {
        return $this->morphMany(TrackingEvent::class, 'trackable');
    }

    /**
     * Observer automatique
     */
    protected static function booted()
    {
        static::updated(function ($colis) {
            if ($colis->wasChanged('etat_colis')) {
                $colis->trackingEvents()->create([
                    'step' => $colis->etat_colis,
                    'label' => match ($colis->etat_colis) {
                        'BL_ENREGISTRE' => 'BL enregistré',
                        'AU_PORT' => 'Arrivé au port',
                        'A_LA_DOUANE' => 'À la douane',
                        'EN_ROUTE' => 'En route',
                        'LIVRE' => 'Livré',
                    },
                    'user_id' => auth()->id(),
                ]);
            }
        });

    }

    public function getTimeline(): array
    {
        $timeline = [];

        // BL
        $timeline[] = [
            'label' => 'BL enregistré',
            'date' => $this->created_at,
            'status' => 'done',
        ];

        // Port
        if ($this->portOperation) {
            $timeline[] = [
                'label' => 'Passage au port',
                'date' => $this->portOperation->date_entree_port,
                'status' => $this->portOperation->status_colis,
            ];
        }

        // Douane
        if ($this->douaneOperation) {
            $timeline[] = [
                'label' => 'Formalités douanières',
                'date' => $this->douaneOperation->date_entree_douane,
                'status' => $this->douaneOperation->status_colis,
            ];
        }

        // Expertise (voiture)
        if ($this->expertise) {
            $timeline[] = [
                'label' => 'Expertise ONT',
                'date' => $this->expertise->updated_at,
                'status' => $this->expertise->status,
            ];
        }

        // Livraison
        if ($this->livraisonOperation) {
            $timeline[] = [
                'label' => 'Livraison',
                'date' => $this->livraisonOperation->date_livraison,
                'status' => $this->livraisonOperation->statut,
            ];
        }

        return $timeline;
    }

}
