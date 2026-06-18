<?php

namespace Database\Seeders;

use App\Models\Buque;
use App\Models\Producto;
use App\Models\TipoCertificado;
use Illuminate\Database\Seeder;

class MaestrosSeeder extends Seeder
{
    public function run(): void
    {
        $buques = [
            ['nombre' => 'MS Northern Star', 'bandera' => 'Panamá', 'numero_imo' => '9123456', 'call_sign' => 'H3PK', 'propietario' => 'Northern Shipping Co.', 'tipo_buque' => 'Carguero'],
            ['nombre' => 'Ocean Explorer II', 'bandera' => 'Uruguay', 'numero_imo' => '9234567', 'call_sign' => 'CXAB', 'propietario' => 'Explorer Marine SA', 'tipo_buque' => 'Pesquero'],
            ['nombre' => 'Heavy Lifter Orion', 'bandera' => 'Liberia', 'numero_imo' => '9345678', 'call_sign' => 'A8OR', 'propietario' => 'Orion Heavy Lift', 'tipo_buque' => 'Buque grúa'],
            ['nombre' => 'Pacific Cargo 7', 'bandera' => 'Islas Marshall', 'numero_imo' => '9456789', 'call_sign' => 'V7PC', 'propietario' => 'Pacific Lines Ltd.', 'tipo_buque' => 'Portacontenedores'],
        ];
        foreach ($buques as $b) {
            Buque::firstOrCreate(['nombre' => $b['nombre']], $b);
        }

        $productos = [
            ['nombre' => 'Chaleco salvavidas inflable', 'categoria' => 'Chaleco', 'subtipo' => 'Inflable', 'descripcion' => 'Chaleco salvavidas inflable SOLAS.'],
            ['nombre' => 'Aparato respiratorio SCBA', 'categoria' => 'Cilindro', 'subtipo' => 'SCBA', 'descripcion' => 'Equipo de respiración autónomo.'],
            ['nombre' => 'Aparato de escape EEBD', 'categoria' => 'Cilindro', 'subtipo' => 'EEBD', 'descripcion' => 'Dispositivo de escape de emergencia.'],
            ['nombre' => 'Traje de inmersión', 'categoria' => 'Traje', 'subtipo' => 'Inmersión', 'descripcion' => 'Traje de supervivencia en agua fría.'],
            ['nombre' => 'Balsa salvavidas inflable', 'categoria' => 'Balsa', 'subtipo' => 'Inflable', 'descripcion' => 'Balsa salvavidas inflable autoenderezable.'],
        ];
        foreach ($productos as $p) {
            Producto::firstOrCreate(['nombre' => $p['nombre']], $p);
        }

        $tipos = [
            ['nombre' => 'Inspección anual de equipos de seguridad', 'intervalo_meses' => 12, 'normativa_aplicable' => 'SOLAS Cap. III', 'descripcion' => 'Inspección anual de dispositivos de salvamento.'],
            ['nombre' => 'Certificación de balsas salvavidas', 'intervalo_meses' => 12, 'normativa_aplicable' => 'SOLAS / MSC.1/Circ.1328', 'descripcion' => 'Servicio y certificación de balsas inflables.'],
            ['nombre' => 'Prueba hidrostática de cilindros', 'intervalo_meses' => 60, 'normativa_aplicable' => 'ISO 9809', 'descripcion' => 'Ensayo de presión de cilindros de aire.'],
        ];
        foreach ($tipos as $t) {
            TipoCertificado::firstOrCreate(['nombre' => $t['nombre']], $t);
        }
    }
}
