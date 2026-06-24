<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Contador ahora por (prefijo, año): cada tipo numera independientemente.
        Schema::dropIfExists('secuencias_certificado');
        Schema::create('secuencias_certificado', function (Blueprint $table) {
            $table->string('prefijo', 10);
            $table->unsignedSmallInteger('anio');
            $table->unsignedInteger('ultimo_numero')->default(0);
            $table->primary(['prefijo', 'anio']);
        });

        // Bolsa de números liberados (al cancelar) para reutilizar.
        Schema::create('numeros_liberados', function (Blueprint $table) {
            $table->id();
            $table->string('prefijo', 10);
            $table->unsignedSmallInteger('anio');
            $table->unsignedInteger('numero');
            $table->unique(['prefijo', 'anio', 'numero']);
        });

        Schema::table('tipos_certificado', function (Blueprint $table) {
            $table->string('prefijo', 10)->nullable()->after('nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('numeros_liberados');
        Schema::dropIfExists('secuencias_certificado');
        Schema::create('secuencias_certificado', function (Blueprint $table) {
            $table->unsignedSmallInteger('anio')->primary();
            $table->unsignedInteger('ultimo_numero')->default(0);
        });
        Schema::table('tipos_certificado', function (Blueprint $table) {
            $table->dropColumn('prefijo');
        });
    }
};
