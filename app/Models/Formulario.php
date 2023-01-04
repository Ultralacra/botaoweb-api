<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formulario extends Model
{
    use HasFactory;
    protected $table = 'formularios';
    protected $fillable = [
        'opcion',
        'tipo_pagina',
        'nombre_ecommerce',
        'cant_ecommerce',
        'foto_ecommerce',
        'nombre_landingpage',
        'info_landingpage',
        'breve_informativa',
        'home_informativa',
        'nosotros_informativa',
        'servicios_informativa',
        'contacto_informativa',
        'pago',
    ];
}
