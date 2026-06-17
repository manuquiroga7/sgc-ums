<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ItemCertificado extends Model
{
    protected $table = 'items_certificado';
    protected $primaryKey = 'id_item';

    protected $fillable = [
        'id_certificado', 'id_producto', 'numero_serie', 'fabricante',
        'modelo', 'fecha_fabricacion', 'aprobacion', 'venc_luz',
        'resultado', 'campos_extra',
    ];

    protected $casts = [
        'fecha_fabricacion' => 'date',
        'venc_luz' => 'date',
        'campos_extra' => 'array',
    ];

    public function certificado(): BelongsTo
    {
        return $this->belongsTo(Certificado::class, 'id_certificado', 'id_certificado');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id_producto');
    }

    public function trabajos(): HasMany
    {
        return $this->hasMany(TrabajoRealizado::class, 'id_item', 'id_item');
    }
}
