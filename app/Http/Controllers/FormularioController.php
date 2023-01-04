<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Formulario;

class FormularioController extends Controller
{
    public function store(request $request)
    {
        $request->validate([
            'opcion'=>'required|max:10',
            'tipo_pagina'=>'required|max:20',
            'nombre_ecommerce'=>'required|max:100',
            'cant_ecommerce'=>'required|max:100',
            'foto_ecommerce'=>'required|max:100',
            'nombre_landingpage'=>'required|max:100',
            'info_landingpage'=>'required|max:100',
            'breve_informativa'=>'required|max:100',
            'home_informativa'=>'required|max:100',
            'nosotros_informativa'=>'required|max:100',
            'servicios_informativa'=>'required|max:100',
            'contacto_informativa'=>'required|max:100',
            'pago'=>'required|max:100',
        ]);

        $formulario = new Formulario;
        $formulario->opcion = $request->opcion;
        $formulario->tipo_pagina = $request->tipo_pagina;
        $formulario->nombre_ecommerce = $request->nombre_ecommerce;
        $formulario->cant_ecommerce = $request->cant_ecommerce;
        $formulario->foto_ecommerce = $request->foto_ecommerce;
        $formulario->nombre_landingpage = $request->nombre_landingpage;
        $formulario->info_landingpage = $request->info_landingpage;
        $formulario->breve_informativa = $request->breve_informativa;
        $formulario->home_informativa = $request->home_informativa;
        $formulario->nosotros_informativa = $request->home_informativa;
        $formulario->servicios_informativa = $request->servicios_informativa;
        $formulario->contacto_informativa = $request->contacto_informativa;
        $formulario->pago = $request->pago;
        $formulario->save();
        return response()->json(['message'=>'Dator registrado'], 200);
    }
}
