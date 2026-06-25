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

        $this->trajeInmersion();
        $this->cilindrosGas();
    }

    // ───── Traje de Inmersión / Antiexposición ─────
    private function trajeInmersion(): void
    {
        $productos = [
            ['nombre' => 'Traje de inmersión LALIZAS NEPTUNE', 'categoria' => 'Traje de Inmersión', 'subtipo' => 'Antiexposición'],
            ['nombre' => 'Traje de inmersión RG RSF-II', 'categoria' => 'Traje de Inmersión', 'subtipo' => 'Antiexposición'],
            ['nombre' => 'Traje de inmersión genérico', 'categoria' => 'Traje de Inmersión', 'subtipo' => 'Antiexposición'],
        ];
        foreach ($productos as $p) {
            Producto::firstOrCreate(['nombre' => $p['nombre']], $p);
        }

        $plantilla = [
            'titulo' => [
                'es' => 'Certificado de Inspección de Trajes de Inmersión / Antiexposición',
                'en' => 'Immersion / Anti-Exposure Suit Inspection Certificate',
            ],
            'intervalo_meses' => 12,
            // Variantes: el intervalo de próximo servicio depende de la elegida.
            'variantes' => [
                ['codigo' => 'A1', 'label' => ['es' => 'Anual (1 año)', 'en' => 'Annual (1 year)'], 'intervalo_meses' => 12],
                ['codigo' => 'A3', 'label' => ['es' => 'Trienal (3 años)', 'en' => 'Triennial (3 years)'], 'intervalo_meses' => 36],
            ],
            'item_fields' => [
                ['key' => 'producto', 'label' => 'Producto / Tipo', 'type' => 'producto_ref', 'categoria' => 'Traje de Inmersión'],
                ['key' => 'numero_serie', 'label' => 'N° de serie / Serial No', 'type' => 'text', 'required' => true],
                ['key' => 'fabricante', 'label' => 'Fabricante / Make', 'type' => 'text'],
                ['key' => 'modelo', 'label' => 'Modelo / Model', 'type' => 'text'],
                ['key' => 'fecha_fabricacion', 'label' => 'Fecha fab. / Date of manuf.', 'type' => 'date'],
                [
                    'key' => 'aprobacion', 'label' => 'Aprobación / Approval', 'type' => 'select',
                    'options' => [
                        ['value' => 'SOLAS', 'label' => 'SOLAS'],
                        ['value' => 'MSC', 'label' => 'MSC'],
                    ],
                ],
                ['key' => 'aislado_termico', 'label' => 'Aislado térm. / Therm. insulated', 'type' => 'boolean'],
                ['key' => 'autoflotante', 'label' => 'Autoflotante / Self buoyant', 'type' => 'boolean'],
                ['key' => 'venc_luz', 'label' => 'Venc. luz / Light exp. date', 'type' => 'date'],
            ],
            'trabajos' => [
                ['codigo' => '1', 'label' => ['es' => 'Inspección visual de todos los componentes, estado y partes adjuntas', 'en' => 'Visual inspection of all components, condition and attached parts']],
                ['codigo' => '2', 'label' => ['es' => 'Prueba de presión de aire realizada', 'en' => 'Air pressure tested']],
                ['codigo' => '3', 'label' => ['es' => 'Nuevo suministrado/instalado', 'en' => 'New supply/installed']],
            ],
            'textos_legales' => [
                [
                    'condicion' => null,
                    'texto' => [
                        'es' => 'Por la presente certificamos que el equipo mencionado fue probado de acuerdo con las directrices del fabricante y la MSC/Circ.1114 "Directrices para las pruebas periódicas de las costuras y cierres de los trajes de inmersión y los trajes de protección contra la intemperie", y se encontró en condiciones adecuadas para su uso.',
                        'en' => 'We hereby certify that the mentioned equipment was tested in accordance with the manufacturer\'s guidelines and MSC/Circ.1114 "Guidelines for periodic testing of seams and closures of immersion suits and anti-exposure suits", and was found in adequate condition for use.',
                    ],
                ],
            ],
            'notas' => [
                [
                    'key' => 'nota_luces',
                    'texto' => [
                        'es' => 'NOTA: Cuando las luces próximas a vencer antes del próximo servicio anual requieren reemplazo, o cuando el equipo se recibe sin luces correspondientes, se recomienda su sustitución o suministro al propietario, capitán o representante. Sin embargo, dicha recomendación ha sido formalmente rechazada por la parte responsable.',
                        'en' => 'NOTE: When lights are approaching expiry before the next annual service or when equipment is received without the corresponding lights, replacement or supply is recommended to the owner, master or their representative. However, such recommendation has been formally declined by the responsible party.',
                    ],
                ],
            ],
        ];

        TipoCertificado::updateOrCreate(
            ['nombre' => 'Traje de Inmersión'],
            [
                'prefijo' => 'TI',
                'intervalo_meses' => 12,
                'normativa_aplicable' => 'MSC/Circ.1114',
                'descripcion' => 'Inspección de trajes de inmersión / antiexposición (variantes 1 y 3 años).',
                'plantilla' => $plantilla,
            ],
        );
    }

    // ───── Cilindros de Gas (Aire / Oxígeno / Nitrógeno) ─────
    private function cilindrosGas(): void
    {
        // El "Tipo" del documento (código 1-6) se modela como producto/subtipo.
        $productos = [
            'Cilindro de Aire SCBA',
            'Cilindro de Aire EEBD',
            'Cilindro de Oxígeno Medicinal',
            'Cilindro de Nitrógeno',
            'Cilindro de Aire para Bote Salvavidas',
            'Cilindro de Otro Tipo',
        ];
        foreach ($productos as $nombre) {
            Producto::firstOrCreate(['nombre' => $nombre], ['categoria' => 'Cilindro de Gas']);
        }

        $textoEs = "Por la presente certificamos que el equipo mencionado fue probado de acuerdo con las directrices del fabricante y se encontró en condiciones correctas de uso, en cumplimiento con las siguientes regulaciones SOLAS:\n".
            "• Cilindro de Aire SCBA → SOLAS Capítulo II-2, Regulación 10.10 (Seguridad contra incendios).\n".
            "• Cilindro de Aire EEBD → SOLAS Capítulo II-2, Regulación 13.4 (Dispositivos de escape de emergencia).\n".
            "• Cilindro de Oxígeno Medicinal → SOLAS Capítulo III, Regulación 4, junto con MFAG (Guía de Primeros Auxilios Médicos) y requisitos del Código IMDG.\n".
            "• Cilindro de Nitrógeno → SOLAS Capítulo II-2, Regulaciones 10 y 4 (Seguridad contra incendios, sistemas de gas inerte).\n".
            "• Cilindro de Aire para Bote Salvavidas → SOLAS Capítulo III, Regulaciones 31 y 34 (Aparatos de salvamento).";
        $textoEn = "We hereby certify that the mentioned equipment was tested according to the manufacturer's guidelines and was found in correct condition of use, in compliance with the following SOLAS regulations:\n".
            "• SCBA Air Cylinder → SOLAS Chapter II-2, Regulation 10.10 (Fire Safety).\n".
            "• EEBD Air Cylinder → SOLAS Chapter II-2, Regulation 13.4 (Emergency Escape Breathing Devices).\n".
            "• Medical Oxygen Cylinder → SOLAS Chapter III, Regulation 4, together with MFAG (Medical First Aid Guide) and IMDG Code requirements.\n".
            "• Nitrogen Cylinder → SOLAS Chapter II-2, Regulations 10 and 4 (Fire Safety, inert gas systems).\n".
            "• Lifeboat Air Cylinder → SOLAS Chapter III, Regulations 31 and 34 (Life-saving appliances).";

        $plantilla = [
            'titulo' => [
                'es' => 'Certificado de Inspección de Cilindros de Gas (Aire / Oxígeno / Nitrógeno)',
                'en' => 'Certificate of Inspection of Gas Cylinders (Air / Oxygen / Nitrogen)',
            ],
            'intervalo_meses' => 12,
            'item_fields' => [
                ['key' => 'producto', 'label' => 'Tipo / Type', 'type' => 'producto_ref', 'categoria' => 'Cilindro de Gas'],
                ['key' => 'numero_serie', 'label' => 'N° de serie / Serial No', 'type' => 'text', 'required' => true],
                ['key' => 'presion_servicio', 'label' => 'Presión de servicio / Pressure service', 'type' => 'text'],
                ['key' => 'volumen', 'label' => 'Volumen (litros) / Volume (litres)', 'type' => 'number'],
                ['key' => 'ultima_prueba_hidraulica', 'label' => 'Última prueba hidráulica / Last hydrotest', 'type' => 'text'],
            ],
            'trabajos' => [
                ['codigo' => '1', 'label' => ['es' => 'Controlado a bordo', 'en' => 'Checked on board']],
                ['codigo' => '2', 'label' => ['es' => 'Inspeccionado en taller', 'en' => 'Inspected in workshop']],
                ['codigo' => '3', 'label' => ['es' => 'Cargado', 'en' => 'Recharged']],
                ['codigo' => '4', 'label' => ['es' => 'Prueba hidráulica', 'en' => 'Hydrostatic test']],
                ['codigo' => '5', 'label' => ['es' => 'Válvula cambiada', 'en' => 'Valve replaced']],
                ['codigo' => '6', 'label' => ['es' => 'Manómetro cambiado', 'en' => 'Pressure gauge replaced']],
                ['codigo' => '7', 'label' => ['es' => 'Válvula reparada', 'en' => 'Valve repaired']],
                ['codigo' => '8', 'label' => ['es' => 'Mantenimiento externo', 'en' => 'External maintenance']],
                ['codigo' => '9', 'label' => ['es' => 'Nuevo', 'en' => 'New']],
                ['codigo' => '10', 'label' => ['es' => 'Otro', 'en' => 'Other']],
            ],
            'textos_legales' => [
                ['condicion' => null, 'texto' => ['es' => $textoEs, 'en' => $textoEn]],
            ],
            'notas' => [],
        ];

        TipoCertificado::updateOrCreate(
            ['nombre' => 'Cilindros de Gas'],
            [
                'prefijo' => 'CG',
                'intervalo_meses' => 12,
                'normativa_aplicable' => 'SOLAS II-2/III · ISO 9809',
                'descripcion' => 'Inspección de cilindros de gas (aire, oxígeno, nitrógeno).',
                'plantilla' => $plantilla,
            ],
        );
    }
}
