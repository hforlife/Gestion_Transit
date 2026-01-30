<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeDossier extends Model
{
    protected $fillable = [
        'nom',
        'description',
    ];
}
