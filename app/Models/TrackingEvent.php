<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingEvent extends Model
{
    //
    protected $fillable = [
        'trackable',
        'step',
        'label',
        'status',
        'commentaire',
        'user_id'
    ];

    public function trackable()
    {
        return $this->morphTo();
    }

    public function colis()
    {
        return $this->belongsTo(Colis::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
