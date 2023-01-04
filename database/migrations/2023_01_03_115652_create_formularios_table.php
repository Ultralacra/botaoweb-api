<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormulariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('formularios', function (Blueprint $table) {
            $table->id();
            $table->string('opcion');
            $table->string('tipo_pagina');
            $table->string('nombre_ecommerce');
            $table->string('cant_ecommerce');
            $table->string('foto_ecommerce');
            $table->string('nombre_landingpage');
            $table->string('info_landingpage');
            $table->string('breve_informativa');
            $table->string('home_informativa');
            $table->string('nosotros_informativa');
            $table->string('servicios_informativa');
            $table->string('contacto_informativa');
            $table->string('pago');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('formularios');
    }
}
