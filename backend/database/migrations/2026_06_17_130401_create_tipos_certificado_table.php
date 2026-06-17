<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_certificado', function (Blueprint $table) {
            $table->id('id_tipo');
            $table->string('nombre');
            $table->unsignedInteger('intervalo_meses')->nullable();
            $table->string('normativa_aplicable')->nullable();
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_certificado');
    }
};
