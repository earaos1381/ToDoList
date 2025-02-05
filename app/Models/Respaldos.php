<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Respaldos extends Model
{
    use HasFactory;
    protected $table = 'respaldos';
    protected $fillable = [
        'nombre',
        'size',
        'archivo',
        'adjunto'
    ];
}
