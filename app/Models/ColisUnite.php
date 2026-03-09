<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ColisUnite extends Model
{
    use HasFactory;

    protected $table = 'colis_unites';

    protected $fillable = [
        'colis_id',

        'type',

        'numero_conteneur',
        'numero_chassis',
        'vin',

        'etat',

        'date_arrivee_port',
        'date_sortie_port',
        'date_livraison',

        'num_t1',
        'etat_t1',
        'declaration_reference',

        'date_entree_douane',
        'date_sortie_douane',

        'num_pvc',
        'etat_pvc',

        'num_ae',
        'etat_ae',

        'num_cmc',
        'etat_cmc',
    ];

    protected $casts = [
        'date_arrivee_port' => 'date',
        'date_sortie_port' => 'date',
        'date_livraison' => 'date',
        'date_entree_douane' => 'date',
        'date_sortie_douane' => 'date',
    ];

    /* =========================================
     | RELATIONS
     ========================================= */

    public function colis()
    {
        return $this->belongsTo(Colis::class);
    }

    /* =========================================
     | HELPERS
     ========================================= */

    public function isConteneur(): bool
    {
        return $this->type === 'CONTENEUR';
    }

    public function isChassis(): bool
    {
        return in_array($this->type, [
            'CHASSIS',
            'CHASSIS_VOITURE',
            'CHASSIS_MACHINE'
        ]);
    }

    public function getNumeroAttribute()
    {
        return $this->numero_conteneur
            ?? $this->numero_chassis
            ?? '-';
    }

    public function getEstLivreAttribute(): bool
    {
        return $this->etat === 'LIVRE';
    }
}
