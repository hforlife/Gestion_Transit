<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'id_dossier_transit',
        'type_document',
        'fichier',
        'valide'
    ];

        public function dossierTransit(): BelongsTo
        {
            return $this->belongsTo(DossierTransit::class, 'id_dossier_transit');
        }

}
