<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    use HasFactory;
    protected $table = 'imagenes';
    protected $fillable = [
        'nombre',
        'ruta',
        'tipo',
        'estado',
    ];
}
