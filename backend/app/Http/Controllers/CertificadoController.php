<?php

namespace App\Http\Controllers;

use App\Models\Certificado;
use App\Models\TipoCertificado;
use Illuminate\Http\JsonResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;

class CertificadoController extends Controller
{
    /** Columnas propias de items_certificado; el resto va a campos_extra. */
    private const KNOWN_ITEM_COLUMNS = [
        'numero_serie', 'fabricante', 'modelo', 'fecha_fabricacion',
        'aprobacion', 'venc_luz', 'resultado',
    ];

    /**
     * Reserva el próximo número para el tipo dado, formato PREFIJO-AÑO-00000001.
     * Reutiliza primero el menor número liberado (cancelado); si no hay, avanza
     * el contador. Todo dentro de una transacción con bloqueo: concurrencia-safe.
     */
    public function reservarNumero(Request $request): JsonResponse
    {
        $request->validate(['id_tipo' => ['required', 'exists:tipos_certificado,id_tipo']]);

        $tipo = TipoCertificado::find($request->id_tipo);
        $prefijo = $tipo->prefijo ?: 'GEN';
        $anio = (int) date('Y');

        DB::table('secuencias_certificado')->insertOrIgnore([
            'prefijo' => $prefijo, 'anio' => $anio, 'ultimo_numero' => 0,
        ]);

        $numero = DB::transaction(function () use ($prefijo, $anio) {
            // Bloquea la fila del contador: serializa todas las reservas de este (prefijo, año).
            $sec = DB::table('secuencias_certificado')
                ->where('prefijo', $prefijo)->where('anio', $anio)
                ->lockForUpdate()->first();

            // ¿Hay un número liberado para reutilizar? (el menor primero)
            $liberado = DB::table('numeros_liberados')
                ->where('prefijo', $prefijo)->where('anio', $anio)
                ->orderBy('numero')->lockForUpdate()->first();

            if ($liberado) {
                DB::table('numeros_liberados')->where('id', $liberado->id)->delete();
                return $liberado->numero;
            }

            $n = $sec->ultimo_numero + 1;
            DB::table('secuencias_certificado')
                ->where('prefijo', $prefijo)->where('anio', $anio)
                ->update(['ultimo_numero' => $n]);

            return $n;
        });

        return response()->json([
            'numero_certificado' => sprintf('%s-%04d-%08d', $prefijo, $anio, $numero),
        ]);
    }

    /**
     * Libera un número reservado (al cancelar/abandonar) para que se reutilice.
     * No libera si el número ya pertenece a un certificado concretado.
     */
    public function liberarNumero(Request $request): JsonResponse
    {
        $request->validate(['numero_certificado' => ['required', 'string']]);

        $partes = $this->parsearNumero($request->numero_certificado);
        if (! $partes) {
            return response()->json(['ok' => false]);
        }

        // No liberar un número que ya quedó asignado a un certificado guardado.
        if (Certificado::where('numero_certificado', $request->numero_certificado)->exists()) {
            return response()->json(['ok' => false]);
        }

        [$prefijo, $anio, $numero] = $partes;
        DB::table('numeros_liberados')->insertOrIgnore([
            'prefijo' => $prefijo, 'anio' => $anio, 'numero' => $numero,
        ]);

        return response()->json(['ok' => true]);
    }

    /** Devuelve [prefijo, anio, numero] o null si el formato no es válido. */
    private function parsearNumero(string $valor): ?array
    {
        if (! preg_match('/^([A-Z0-9]+)-(\d{4})-(\d{1,8})$/', trim($valor), $m)) {
            return null;
        }

        return [$m[1], (int) $m[2], (int) $m[3]];
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
            'variante' => ['nullable', 'string', 'max:20'],
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
                'datos_extra' => ! empty($data['variante']) ? ['variante' => $data['variante']] : null,
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

    public function pdf(Certificado $certificado, Request $request): \Illuminate\Http\Response
    {
        $certificado->load(['buque', 'tipo', 'items.producto', 'items.trabajos']);

        $plantilla = is_array($certificado->tipo->plantilla)
            ? $certificado->tipo->plantilla
            : json_decode($certificado->tipo->plantilla, true) ?? [];

        $recomendaciones = $certificado->recomendaciones;
        if ($certificado->idioma === 'en' && $recomendaciones) {
            try {
                $translator = new GoogleTranslate('en');
                $translator->setSource('es');
                $translator->setOptions(['verify' => false]);
                $translated = $translator->translate($recomendaciones);
                if ($translated) {
                    $recomendaciones = $translated;
                }
            } catch (\Exception $e) {
                // Si falla la traducción, usar el texto original
            }
        }

        $pdf = Pdf::loadView('pdf.certificado', [
            'certificado'     => $certificado,
            'plantilla'       => $plantilla,
            'recomendaciones' => $recomendaciones,
        ])->setPaper('a4', 'portrait');

        $dompdf = $pdf->getDomPDF();
        $dompdf->render();
        $canvas = $dompdf->getCanvas();
        $canvas->page_text(
            $canvas->get_width() - 90,
            $canvas->get_height() - 28,
            'Pág. {PAGE_NUM} / {PAGE_COUNT}',
            null,
            8,
            [0.6, 0.6, 0.6]
        );

        $filename = ($certificado->numero_certificado ?? 'certificado') . '.pdf';

        if ($request->boolean('download')) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }
}
