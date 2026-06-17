<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrabajoRealizado extends Model
{
    protected $table = 'trabajos_realizados';
    protected $primaryKey = 'id_trabajo';

    protected $fillable = [
        'id_item', 'codigo_trabajo', 'descripcion', 'fecha',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(ItemCertificado::class, 'id_item', 'id_item');
    }
}
