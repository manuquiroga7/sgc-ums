<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipos_certificado', function (Blueprint $table) {
            // Definición dinámica del tipo (campos del ítem, trabajos, textos legales, notas).
            $table->json('plantilla')->nullable()->after('descripcion');
        });

        Schema::table('certificados', function (Blueprint $table) {
            $table->string('idioma', 5)->default('es')->after('estado');   // idioma del PDF
            $table->json('datos_extra')->nullable()->after('idioma');       // extras de encabezado
        });
    }

    public function down(): void
    {
        Schema::table('tipos_certificado', function (Blueprint $table) {
            $table->dropColumn('plantilla');
        });
        Schema::table('certificados', function (Blueprint $table) {
            $table->dropColumn(['idioma', 'datos_extra']);
        });
    }
};
