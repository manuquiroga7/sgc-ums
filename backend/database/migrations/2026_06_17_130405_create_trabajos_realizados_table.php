<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trabajos_realizados', function (Blueprint $table) {
            $table->id('id_trabajo');
            $table->foreignId('id_item')->constrained('items_certificado', 'id_item')->cascadeOnDelete();
            $table->string('codigo_trabajo')->nullable();
            $table->text('descripcion')->nullable();
            $table->date('fecha')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trabajos_realizados');
    }
};
