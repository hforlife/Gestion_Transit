<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortOperation extends Model
{
protected $fillable = [
        'date_entree_port','date_sortie_port',
        'status_colis','colis_id','agent_id'
    ];

    public function colis() {
        return $this->belongsTo(Colis::class);
    }

    public function agent() {
        return $this->belongsTo(User::class);
    }

    public function updateStatus($status) {
        $this->status_colis = $status;
        $this->save();
    }
}
