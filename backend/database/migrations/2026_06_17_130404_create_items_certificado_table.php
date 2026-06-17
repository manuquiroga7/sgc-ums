<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items_certificado', function (Blueprint $table) {
            $table->id('id_item');
            $table->foreignId('id_certificado')->constrained('certificados', 'id_certificado')->cascadeOnDelete();
            $table->foreignId('id_producto')->constrained('productos', 'id_producto')->restrictOnDelete();
            $table->string('numero_serie')->nullable();
            $table->string('fabricante')->nullable();
            $table->string('modelo')->nullable();
            $table->date('fecha_fabricacion')->nullable();
            $table->string('aprobacion')->nullable();   // SOLAS/MSC
            $table->date('venc_luz')->nullable();
            $table->string('resultado')->nullable();     // OK/Rechazado
            $table->json('campos_extra')->nullable();    // presion, volumen, aislamiento... (campos flexibles por tipo)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items_certificado');
    }
};
