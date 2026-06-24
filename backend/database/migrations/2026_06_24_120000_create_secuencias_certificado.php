<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Contador por año para asignar números de certificado de forma atómica.
        Schema::create('secuencias_certificado', function (Blueprint $table) {
            $table->unsignedSmallInteger('anio')->primary();
            $table->unsignedInteger('ultimo_numero')->default(0);
        });

        // El número de certificado no se puede repetir (admite múltiples NULL en MySQL).
        Schema::table('certificados', function (Blueprint $table) {
            $table->unique('numero_certificado');
        });
    }

    public function down(): void
    {
        Schema::table('certificados', function (Blueprint $table) {
            $table->dropUnique('certificados_numero_certificado_unique');
        });
        Schema::dropIfExists('secuencias_certificado');
    }
};
