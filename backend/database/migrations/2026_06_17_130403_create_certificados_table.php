<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificados', function (Blueprint $table) {
            $table->id('id_certificado');
            $table->foreignId('id_buque')->constrained('buques', 'id_buque')->cascadeOnDelete();
            $table->foreignId('id_tipo')->constrained('tipos_certificado', 'id_tipo')->restrictOnDelete();
            $table->string('numero_certificado')->nullable();
            $table->date('fecha_emision')->nullable();
            $table->date('fecha_proximo_servicio')->nullable();
            $table->string('inspector')->nullable();
            $table->string('empresa_certificadora')->nullable();
            $table->unsignedInteger('total_unidades')->nullable();
            $table->text('recomendaciones')->nullable();
            $table->string('estado')->default('borrador'); // borrador, certificado, pendiente, no_cumple, vencido
            $table->string('archivo_doc')->nullable();      // path al PDF generado
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificados');
    }
};
