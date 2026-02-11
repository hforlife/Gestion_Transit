<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expertise extends Model
{
    //
    protected $fillable = [
        'num_pvc','num_ae','num_cmc',
        'etat_expertise','etat_pvc','etat_ae','etat_cmc',
        'status','expert_id','colis_id'
    ];

    public function colis() {
        return $this->belongsTo(Colis::class);
    }

    public function expert() {
        return $this->belongsTo(User::class, 'expert_id');
    }

    public function assignAgent($userId) {
        $this->expert_id = $userId;
        $this->save();
    }
}
