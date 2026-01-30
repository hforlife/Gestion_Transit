<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DouaneOperation extends Model
{
    protected $fillable = [
        'num_t1',
        'etat_t1',
        'declaration_reference',
        'date_entree_douane',
        'date_sortie_douane',
        'status_colis',
        'colis_id',
        'agent_id'
    ];

    public function colis()
    {
        return $this->belongsTo(Colis::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class);
    }

    public function updateStatus($status)
    {
        $this->status_colis = $status;
        $this->save();
    }
}
