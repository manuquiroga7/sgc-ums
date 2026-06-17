<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Certificado extends Model
{
    protected $table = 'certificados';
    protected $primaryKey = 'id_certificado';

    protected $fillable = [
        'id_buque', 'id_tipo', 'numero_certificado', 'fecha_emision',
        'fecha_proximo_servicio', 'inspector', 'empresa_certificadora',
        'total_unidades', 'recomendaciones', 'estado', 'archivo_doc',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'fecha_proximo_servicio' => 'date',
        'fecha_creacion' => 'datetime',
    ];

    public function buque(): BelongsTo
    {
        return $this->belongsTo(Buque::class, 'id_buque', 'id_buque');
    }

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoCertificado::class, 'id_tipo', 'id_tipo');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ItemCertificado::class, 'id_certificado', 'id_certificado');
    }
}
