<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buques', function (Blueprint $table) {
            $table->id('id_buque');
            $table->string('nombre');
            $table->string('bandera')->nullable();
            $table->string('numero_imo')->nullable();
            $table->string('call_sign')->nullable();
            $table->string('propietario')->nullable();
            $table->string('tipo_buque')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buques');
    }
};
