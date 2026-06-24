<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoCertificado extends Model
{
    protected $table = 'tipos_certificado';
    protected $primaryKey = 'id_tipo';

    protected $fillable = [
        'nombre', 'prefijo', 'intervalo_meses', 'normativa_aplicable', 'descripcion', 'plantilla',
    ];

    protected $casts = [
        'plantilla' => 'array',
    ];

    public function certificados(): HasMany
    {
        return $this->hasMany(Certificado::class, 'id_tipo', 'id_tipo');
    }
}
