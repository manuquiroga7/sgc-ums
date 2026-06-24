<?php

namespace Database\Seeders;

use App\Models\Producto;
use App\Models\TipoCertificado;
use Illuminate\Database\Seeder;

class PlantillasSeeder extends Seeder
{
    public function run(): void
    {
        // Productos identificados como "Traje de Bombero" (categoría = atributo identificatorio).
        $productosBombero = [
            ['nombre' => 'Traje de bombero FIRE BUDDY', 'categoria' => 'Traje de Bombero', 'subtipo' => 'Completo'],
            ['nombre' => 'Traje de bombero HAIZHOU', 'categoria' => 'Traje de Bombero', 'subtipo' => 'Completo'],
            ['nombre' => 'Traje de bombero genérico', 'categoria' => 'Traje de Bombero', 'subtipo' => 'Completo'],
        ];
        foreach ($productosBombero as $p) {
            Producto::firstOrCreate(['nombre' => $p['nombre']], $p);
        }

        // ───── Traje de Bombero ─────
        $bombero = [
            'titulo' => [
                'es' => 'Certificado de Inspección de Trajes de Bombero',
                'en' => "Certificate of Inspection of Fire-fighter's Outfit",
            ],
            'intervalo_meses' => 12,
            'item_fields' => [
                ['key' => 'producto', 'label' => 'Producto / Tipo', 'type' => 'producto_ref', 'categoria' => 'Traje de Bombero'],
                ['key' => 'fabricante', 'label' => 'Fabricante / Make', 'type' => 'text'],
                ['key' => 'modelo', 'label' => 'Modelo / Model', 'type' => 'text'],
                ['key' => 'numero_serie', 'label' => 'N° de serie / Serial No', 'type' => 'text', 'required' => true],
                ['key' => 'chaqueta', 'label' => 'Chaqueta / Jacket', 'type' => 'number'],
                ['key' => 'pantalon', 'label' => 'Pantalón / Pants', 'type' => 'number'],
                ['key' => 'guantes', 'label' => 'Guantes / Gloves', 'type' => 'number'],
                ['key' => 'botas', 'label' => 'Botas / Boots', 'type' => 'number'],
                ['key' => 'casco', 'label' => 'Casco / Helmet', 'type' => 'number'],
                ['key' => 'protector_facial', 'label' => 'Protector facial / Face shield', 'type' => 'number'],
                ['key' => 'cuerda_vida', 'label' => 'Cuerda de vida / Rope life', 'type' => 'number'],
                ['key' => 'cinturon', 'label' => 'Cinturón / Belt', 'type' => 'number'],
                ['key' => 'linterna', 'label' => 'Linterna / Safety Lamp', 'type' => 'text'],
                [
                    'key' => 'resultado', 'label' => 'Verificado / Checked', 'type' => 'select',
                    'options' => [
                        ['value' => 'OK', 'label' => 'OK'],
                        ['value' => 'Rechazado', 'label' => 'Rechazado / Rejected'],
                    ],
                ],
            ],
            'trabajos' => [
                ['codigo' => '1', 'label' => ['es' => 'Inspección visual de todos los componentes, estado y partes adjuntas', 'en' => 'Visual inspection of all components, condition and attached parts']],
                ['codigo' => '2', 'label' => ['es' => 'Nuevo suministrado/instalado', 'en' => 'New supply/installed']],
                ['codigo' => '3', 'label' => ['es' => 'Rechazado', 'en' => 'Rejected']],
            ],
            'textos_legales' => [
                [
                    'condicion' => null,
                    'texto' => [
                        'es' => 'Por la presente se certifica que el equipo anteriormente mencionado ha sido inspeccionado y probado de acuerdo con las instrucciones del fabricante y los requisitos del Convenio SOLAS, Capítulo II-2, Regla 10, y del Código Internacional de Sistemas de Seguridad contra Incendios (FSS Code), adoptado mediante la Resolución MSC.98(73) de la OMI. Como resultado de dicha inspección y prueba, se constató que el equipo se encuentra en condiciones satisfactorias y apto para el servicio previsto.',
                        'en' => "We hereby certify that the above-mentioned equipment has been inspected and tested in accordance with the manufacturer's guidelines and the requirements of SOLAS Chapter II-2, Regulation 10, and the International Code for Fire Safety Systems (FSS Code), adopted by IMO Resolution MSC.98(73). The equipment was found to be in satisfactory condition and fit for its intended service.",
                    ],
                ],
            ],
            'notas' => [],
        ];

        TipoCertificado::updateOrCreate(
            ['nombre' => 'Traje de Bombero'],
            [
                'prefijo' => 'TB',
                'intervalo_meses' => 12,
                'normativa_aplicable' => 'SOLAS II-2/10 · FSS Code',
                'descripcion' => 'Inspección de trajes de bombero y sus componentes.',
                'plantilla' => $bombero,
            ],
        );

        // Prefijos para los tipos sembrados por MaestrosSeeder.
        TipoCertificado::where('nombre', 'Certificación de balsas salvavidas')->update(['prefijo' => 'BS']);
        TipoCertificado::where('nombre', 'Inspección anual de equipos de seguridad')->update(['prefijo' => 'IA']);
        TipoCertificado::where('nombre', 'Prueba hidrostática de cilindros')->update(['prefijo' => 'PH']);
    }
}
