<?php

namespace App\Http\Controllers;

use App\Models\Certificado;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CertificadoController extends Controller
{
    /** Columnas propias de items_certificado; el resto va a campos_extra. */
    private const KNOWN_ITEM_COLUMNS = [
        'numero_serie', 'fabricante', 'modelo', 'fecha_fabricacion',
        'aprobacion', 'venc_luz', 'resultado',
    ];

    /**
     * Reserva atómicamente el próximo número de certificado del año en curso.
     * El contador solo avanza: el número queda consumido aunque la certificación
     * no se complete, evitando colisiones entre usuarios concurrentes.
     */
    public function reservarNumero(): JsonResponse
    {
        $anio = (int) date('Y');

        // Asegura la fila del año (idempotente, sin condición de carrera).
        DB::table('secuencias_certificado')->insertOrIgnore(['anio' => $anio, 'ultimo_numero' => 0]);

        $proximo = DB::transaction(function () use ($anio) {
            $sec = DB::table('secuencias_certificado')
                ->where('anio', $anio)
                ->lockForUpdate()
                ->first();

            $n = $sec->ultimo_numero + 1;
            DB::table('secuencias_certificado')->where('anio', $anio)->update(['ultimo_numero' => $n]);

            return $n;
        });

        $numero = sprintf('%04d/%02d', $proximo, $anio % 100);

        return response()->json(['numero_certificado' => $numero]);
    }

    public function index(): JsonResponse
    {
        $certificados = Certificado::with(['buque', 'tipo'])
            ->orderByDesc('id_certificado')
            ->get();

        return response()->json($certificados);
    }

    public function show(Certificado $certificado): JsonResponse
    {
        $certificado->load(['buque', 'tipo', 'items.producto', 'items.trabajos']);

        return response()->json($certificado);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_buque' => ['required', 'exists:buques,id_buque'],
            'id_tipo' => ['required', 'exists:tipos_certificado,id_tipo'],
            'numero_certificado' => ['nullable', 'string', 'max:191', 'unique:certificados,numero_certificado'],
            'fecha_emision' => ['nullable', 'date'],
            'fecha_proximo_servicio' => ['nullable', 'date'],
            'inspector' => ['nullable', 'string', 'max:191'],
            'recomendaciones' => ['nullable', 'string'],
            'idioma' => ['nullable', 'in:es,en'],
            'estado' => ['nullable', 'string', 'max:191'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id_producto' => ['nullable', 'exists:productos,id_producto'],
            'items.*.campos' => ['nullable', 'array'],
            'items.*.trabajos' => ['nullable', 'array'],
        ]);

        $certificado = DB::transaction(function () use ($data) {
            $cert = Certificado::create([
                'id_buque' => $data['id_buque'],
                'id_tipo' => $data['id_tipo'],
                'numero_certificado' => $data['numero_certificado'] ?? null,
                'fecha_emision' => $data['fecha_emision'] ?? null,
                'fecha_proximo_servicio' => $data['fecha_proximo_servicio'] ?? null,
                'inspector' => $data['inspector'] ?? null,
                'empresa_certificadora' => 'Uruguayan Marine Safety Ltd.',
                'recomendaciones' => $data['recomendaciones'] ?? null,
                'idioma' => $data['idioma'] ?? 'es',
                'estado' => $data['estado'] ?? 'borrador',
                'total_unidades' => count($data['items']),
            ]);

            foreach ($data['items'] as $itemData) {
                $campos = $itemData['campos'] ?? [];

                // Separar columnas conocidas de los campos dinámicos (campos_extra).
                $columns = ['id_producto' => $itemData['id_producto'] ?? null];
                $extra = [];
                foreach ($campos as $key => $value) {
                    if (in_array($key, self::KNOWN_ITEM_COLUMNS, true)) {
                        $columns[$key] = $value;
                    } else {
                        $extra[$key] = $value;
                    }
                }
                $columns['campos_extra'] = $extra ?: null;

                $item = $cert->items()->create($columns);

                foreach ($itemData['trabajos'] ?? [] as $codigo) {
                    $item->trabajos()->create([
                        'codigo_trabajo' => (string) $codigo,
                        'fecha' => $data['fecha_emision'] ?? null,
                    ]);
                }
            }

            return $cert;
        });

        $certificado->load(['buque', 'tipo', 'items.trabajos']);

        return response()->json($certificado, 201);
    }
}
