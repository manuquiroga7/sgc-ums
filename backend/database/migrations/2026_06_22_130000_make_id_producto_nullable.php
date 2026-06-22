<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items_certificado', function (Blueprint $table) {
            // El producto/subtipo es opcional por ítem (depende del tipo de certificado).
            $table->dropForeign(['id_producto']);
            $table->foreignId('id_producto')->nullable()->change();
            $table->foreign('id_producto')->references('id_producto')->on('productos')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('items_certificado', function (Blueprint $table) {
            $table->dropForeign(['id_producto']);
            $table->foreignId('id_producto')->nullable(false)->change();
            $table->foreign('id_producto')->references('id_producto')->on('productos')->restrictOnDelete();
        });
    }
};
