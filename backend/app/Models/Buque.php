<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Buque extends Model
{
    protected $table = 'buques';
    protected $primaryKey = 'id_buque';

    protected $fillable = [
        'nombre', 'bandera', 'numero_imo', 'call_sign',
        'propietario', 'tipo_buque', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function certificados(): HasMany
    {
        return $this->hasMany(Certificado::class, 'id_buque', 'id_buque');
    }
}
