<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeColis extends Model
{
        protected $fillable = [
        'nom',
        'description',
        'is_active'
    ];

    public function colis()
    {
        return $this->hasMany(Colis::class, 'id_type_colis');
    }
}
