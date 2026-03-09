<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Colis extends Model
{
    use HasFactory;

    protected $table = 'colis';

    protected $fillable = [
        'numero_bl',
        'description',
        'id_type_colis',
        'user_id',
        'id_port',
        'etat_colis',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /* =========================================
     | RELATIONS
     ========================================= */

    public function typeColis()
    {
        return $this->belongsTo(TypeColis::class, 'id_type_colis');
    }

    public function port()
    {
        return $this->belongsTo(Port::class, 'id_port');
    }

    public function agent()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function unites()
    {
        return $this->hasMany(ColisUnite::class);
    }

    public function dossierTransit()
    {
        return $this->belongsTo(DossierTransit::class, 'id_dossier_transit');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /* =========================================
     | ACCESSORS UTILES
     ========================================= */

    public function getTotalUnitesAttribute()
    {
        return $this->unites()->count();
    }

    public function getUnitesLivreesAttribute()
    {
        return $this->unites()
            ->where('etat', 'LIVRE')
            ->count();
    }

    public function getProgressionAttribute()
    {
        $total = $this->total_unites;

        if ($total === 0) {
            return 0;
        }

        return round(($this->unites_livrees / $total) * 100);
    }

    /* =========================================
     | STATISTIQUES
     ========================================= */

    public function statsUnites()
    {
        return [
            'AU_PORT' => $this->unites()->where('etat', 'AU_PORT')->count(),
            'A_LA_DOUANE' => $this->unites()->where('etat', 'A_LA_DOUANE')->count(),
            'EXPERTISE' => $this->unites()->where('etat', 'EXPERTISE')->count(),
            'EN_ROUTE' => $this->unites()->where('etat', 'EN_ROUTE')->count(),
            'LIVRE' => $this->unites()->where('etat', 'LIVRE')->count(),
        ];
    }
}

