# PDF Certificado Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Generar un PDF del certificado en el backend Laravel (DomPDF) y exponerlo via `GET /api/certificados/{id}/pdf`, con botones de Ver/Descargar en la pantalla de éxito del wizard Angular.

**Architecture:** Laravel renderiza una vista Blade como PDF usando barryvdh/laravel-dompdf. El token Sanctum se pasa como query param `?token=` via un middleware custom que lo mueve al header `Authorization`. Angular construye la URL con el token y la abre en nueva pestaña o descarga.

**Tech Stack:** Laravel 13, barryvdh/laravel-dompdf, Blade, Angular 21 (signals), Tailwind CSS v4

---

## Mapa de archivos

**Backend — crear:**
- `resources/views/pdf/certificado.blade.php` — HTML del PDF (A4, bilingüe, tabla dinámica)
- `app/Http/Middleware/TokenFromQuery.php` — middleware que mueve `?token=` al header Authorization

**Backend — modificar:**
- `composer.json` / `vendor/` — instalar barryvdh/laravel-dompdf
- `app/Http/Controllers/CertificadoController.php` — agregar método `pdf()`
- `routes/api.php` — nueva ruta GET con middleware `token.from.query`
- `bootstrap/app.php` — registrar el alias del middleware

**Frontend — modificar:**
- `src/app/core/api.service.ts` — agregar método `pdfUrl(id, download?)`
- `src/app/features/certificacion/wizard.ts` — inyectar AuthService, exponer `savedId`
- `src/app/features/certificacion/wizard.html` — botones Ver PDF / Descargar en pantalla de éxito

---

## Task 1: Instalar DomPDF

**Files:**
- Modify: `C:\wamp64\www\SGC-UMS\backend\composer.json` (vía composer)

- [ ] **Step 1: Instalar el paquete**

```powershell
cd C:\wamp64\www\SGC-UMS\backend
composer require barryvdh/laravel-dompdf
```

Salida esperada: `Package operations: 3 installs` (dompdf, dompdf/dompdf, phenx/php-svg-lib).

- [ ] **Step 2: Publicar config**

```powershell
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

Salida esperada: `Copied File [...] config/dompdf.php`.

- [ ] **Step 3: Commit**

```powershell
git -C C:\wamp64\www\SGC-UMS add backend/composer.json backend/composer.lock backend/config/dompdf.php
git -C C:\wamp64\www\SGC-UMS commit -m "chore(backend): install barryvdh/laravel-dompdf"
```

---

## Task 2: Middleware TokenFromQuery

Sanctum no acepta el token via query param por defecto. Este middleware lee `?token=` y lo inyecta en el header `Authorization: Bearer` antes de que llegue a Sanctum.

**Files:**
- Create: `C:\wamp64\www\SGC-UMS\backend\app\Http\Middleware\TokenFromQuery.php`
- Modify: `C:\wamp64\www\SGC-UMS\backend\bootstrap\app.php`

- [ ] **Step 1: Crear el middleware**

Crear el archivo `app/Http/Middleware/TokenFromQuery.php` con este contenido exacto:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenFromQuery
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->query('token');

        if ($token && ! $request->bearerToken()) {
            $request->headers->set('Authorization', 'Bearer ' . $token);
        }

        return $next($request);
    }
}
```

- [ ] **Step 2: Registrar alias en bootstrap/app.php**

Abrir `C:\wamp64\www\SGC-UMS\backend\bootstrap\app.php`. Dentro de `->withMiddleware(function (Middleware $middleware) {`, agregar antes del cierre `})`:

```php
$middleware->alias([
    'token.from.query' => \App\Http\Middleware\TokenFromQuery::class,
]);
```

El bloque `withMiddleware` debe quedar así:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'token.from.query' => \App\Http\Middleware\TokenFromQuery::class,
    ]);
})
```

- [ ] **Step 3: Commit**

```powershell
git -C C:\wamp64\www\SGC-UMS add backend/app/Http/Middleware/TokenFromQuery.php backend/bootstrap/app.php
git -C C:\wamp64\www\SGC-UMS commit -m "feat(backend): add TokenFromQuery middleware for PDF download auth"
```

---

## Task 3: Vista Blade del PDF

**Files:**
- Create: `C:\wamp64\www\SGC-UMS\backend\resources\views\pdf\certificado.blade.php`

El template recibe dos variables: `$certificado` (Eloquent con relaciones cargadas) y `$plantilla` (array PHP desde el JSON de la plantilla).

- [ ] **Step 1: Crear directorio y vista**

Crear `resources/views/pdf/certificado.blade.php`:

```blade
<!DOCTYPE html>
<html lang="{{ $certificado->idioma ?? 'es' }}">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 9pt;
    color: #1a1a2e;
    background: #fff;
  }

  /* ── Encabezado ── */
  .header {
    background: #1e3a5f;
    color: #fff;
    padding: 14px 20px;
    display: table;
    width: 100%;
  }
  .header-logo {
    display: table-cell;
    width: 120px;
    vertical-align: middle;
  }
  .header-logo .brand {
    font-size: 22pt;
    font-weight: bold;
    letter-spacing: 2px;
    color: #a8d5ff;
  }
  .header-logo .brand-sub {
    font-size: 7pt;
    color: #cce0ff;
    margin-top: 2px;
  }
  .header-title {
    display: table-cell;
    vertical-align: middle;
    text-align: right;
  }
  .header-title h1 {
    font-size: 12pt;
    font-weight: bold;
    text-transform: uppercase;
    line-height: 1.3;
  }
  .header-title .cert-num {
    font-size: 10pt;
    color: #a8d5ff;
    margin-top: 4px;
  }

  /* ── Secciones ── */
  .section {
    margin: 10px 20px 0;
    border: 1px solid #c5d5e8;
    border-radius: 4px;
    overflow: hidden;
  }
  .section-title {
    background: #e8f0f8;
    color: #1e3a5f;
    font-size: 7.5pt;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 5px 10px;
    border-bottom: 1px solid #c5d5e8;
  }
  .section-body {
    padding: 8px 10px;
  }

  /* ── Grid de datos del buque ── */
  .data-grid {
    display: table;
    width: 100%;
  }
  .data-row {
    display: table-row;
  }
  .data-cell {
    display: table-cell;
    padding: 3px 8px 3px 0;
    vertical-align: top;
  }
  .data-label {
    font-size: 7pt;
    color: #5a6a7e;
    text-transform: uppercase;
    letter-spacing: 0.3px;
  }
  .data-value {
    font-size: 9pt;
    font-weight: bold;
  }

  /* ── Tabla de ítems ── */
  .items-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 7.5pt;
  }
  .items-table th {
    background: #1e3a5f;
    color: #fff;
    padding: 5px 6px;
    text-align: left;
    font-size: 7pt;
    font-weight: bold;
    text-transform: uppercase;
  }
  .items-table td {
    padding: 5px 6px;
    border-bottom: 1px solid #e0e8f0;
    vertical-align: top;
  }
  .items-table tr:nth-child(even) td {
    background: #f4f8fc;
  }

  /* ── Datos del certificado ── */
  .cert-data {
    display: table;
    width: 100%;
  }
  .cert-data-col {
    display: table-cell;
    width: 33%;
    padding-right: 10px;
    vertical-align: top;
  }

  /* ── Nota de luces ── */
  .nota-luces {
    margin: 10px 20px 0;
    background: #fff8e1;
    border: 1px solid #f9c74f;
    border-radius: 4px;
    padding: 8px 10px;
    font-size: 7.5pt;
    color: #5a4000;
  }

  /* ── Texto legal ── */
  .legal {
    margin: 10px 20px 0;
    background: #f0f4f8;
    border: 1px solid #c5d5e8;
    border-radius: 4px;
    padding: 10px;
    font-size: 7pt;
    color: #3a4a5e;
    line-height: 1.5;
  }
  .legal-title {
    font-size: 7pt;
    font-weight: bold;
    text-transform: uppercase;
    color: #1e3a5f;
    margin-bottom: 4px;
  }

  /* ── Firma ── */
  .firma {
    display: table;
    width: 100%;
    margin: 14px 20px 0;
    padding-bottom: 20px;
  }
  .firma-col {
    display: table-cell;
    width: 50%;
    padding-right: 20px;
    vertical-align: bottom;
  }
  .firma-line {
    border-top: 1px solid #1e3a5f;
    padding-top: 4px;
    font-size: 7.5pt;
    color: #3a4a5e;
  }

  /* ── Footer ── */
  .footer {
    margin: 12px 20px 0;
    border-top: 1px solid #c5d5e8;
    padding-top: 6px;
    font-size: 6.5pt;
    color: #8a9ab0;
    display: table;
    width: 100%;
  }
  .footer-left { display: table-cell; }
  .footer-right { display: table-cell; text-align: right; }
</style>
</head>
<body>

@php
  $lang = $certificado->idioma ?? 'es';
  $t = fn($obj) => is_array($obj) ? ($obj[$lang] ?? $obj['es'] ?? '') : ($obj ?? '');
  $pl = $plantilla;
  $tipo = $certificado->tipo;
  $buque = $certificado->buque;

  // Campos de la plantilla que NO son producto_ref
  $itemFields = collect($pl['item_fields'] ?? [])
    ->filter(fn($f) => ($f['type'] ?? '') !== 'producto_ref')
    ->values();

  // Si algún ítem tiene venc_luz: mostrar nota de luces
  $tieneNotaLuces = collect($pl['notas'] ?? [])->contains(fn($n) => ($n['key'] ?? '') === 'nota_luces');
  $notaLucesTexto = '';
  if ($tieneNotaLuces) {
    $nota = collect($pl['notas'])->firstWhere('key', 'nota_luces');
    $notaLucesTexto = $t($nota['texto'] ?? '');
  }

  // Trabajos lookup: codigo => label
  $trabajosMap = collect($pl['trabajos'] ?? [])->keyBy('codigo');

  // Textos legales (solo los sin condición)
  $textosLegales = collect($pl['textos_legales'] ?? [])
    ->filter(fn($tl) => ($tl['condicion'] ?? null) === null)
    ->map(fn($tl) => $t($tl['texto'] ?? ''))
    ->filter()
    ->values();

  $fechaEmision = $certificado->fecha_emision
    ? \Carbon\Carbon::parse($certificado->fecha_emision)->format('d/m/Y')
    : '—';
  $fechaProximo = $certificado->fecha_proximo_servicio
    ? \Carbon\Carbon::parse($certificado->fecha_proximo_servicio)->format('d/m/Y')
    : '—';

  $tituloEs = $pl['titulo']['es'] ?? $tipo->nombre ?? 'Certificado';
  $tituloEn = $pl['titulo']['en'] ?? $tipo->nombre ?? 'Certificate';
@endphp

{{-- ═══════════════════════════════════════════════ --}}
{{--  ENCABEZADO                                     --}}
{{-- ═══════════════════════════════════════════════ --}}
<div class="header">
  <div class="header-logo">
    <div class="brand">UMS</div>
    <div class="brand-sub">Uruguayan Marine Safety Ltd.</div>
  </div>
  <div class="header-title">
    <h1>
      {{ $tituloEs }}<br>
      <span style="font-size:9.5pt;font-weight:normal;color:#cce0ff;">{{ $tituloEn }}</span>
    </h1>
    <div class="cert-num">N° {{ $certificado->numero_certificado ?? '—' }}</div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════ --}}
{{--  BUQUE / VESSEL                                 --}}
{{-- ═══════════════════════════════════════════════ --}}
<div class="section">
  <div class="section-title">Buque / Vessel</div>
  <div class="section-body">
    <div class="data-grid">
      <div class="data-row">
        <div class="data-cell" style="width:28%">
          <div class="data-label">Nombre / Name</div>
          <div class="data-value">{{ $buque->nombre ?? '—' }}</div>
        </div>
        <div class="data-cell" style="width:18%">
          <div class="data-label">Bandera / Flag</div>
          <div class="data-value">{{ $buque->bandera ?? '—' }}</div>
        </div>
        <div class="data-cell" style="width:20%">
          <div class="data-label">N° IMO</div>
          <div class="data-value" style="font-family:monospace;">{{ $buque->numero_imo ?? '—' }}</div>
        </div>
        <div class="data-cell" style="width:18%">
          <div class="data-label">Call Sign</div>
          <div class="data-value" style="font-family:monospace;">{{ $buque->call_sign ?? '—' }}</div>
        </div>
        <div class="data-cell">
          <div class="data-label">Propietario / Owner</div>
          <div class="data-value">{{ $buque->propietario ?? '—' }}</div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════ --}}
{{--  TABLA DE ÍTEMS                                 --}}
{{-- ═══════════════════════════════════════════════ --}}
<div class="section">
  <div class="section-title">
    Equipos inspeccionados / Equipment inspected
    ({{ $certificado->items->count() }} {{ $certificado->items->count() === 1 ? 'unidad/unit' : 'unidades/units' }})
  </div>
  <div class="section-body" style="padding:0;">
    <table class="items-table">
      <thead>
        <tr>
          <th style="width:28px;">#</th>
          <th>{{ $lang === 'es' ? 'Producto' : 'Product' }}</th>
          @foreach ($itemFields as $field)
            <th>{{ $t($field['label']) }}</th>
          @endforeach
          <th>{{ $lang === 'es' ? 'Trabajos realizados' : 'Works performed' }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($certificado->items as $i => $item)
          @php
            $campos = array_merge(
              (array) ($item->campos_extra ?? []),
              array_filter([
                'numero_serie'     => $item->numero_serie,
                'fabricante'       => $item->fabricante,
                'modelo'           => $item->modelo,
                'fecha_fabricacion'=> $item->fecha_fabricacion,
                'aprobacion'       => $item->aprobacion,
                'venc_luz'         => $item->venc_luz,
                'resultado'        => $item->resultado,
              ], fn($v) => $v !== null)
            );

            $trabajosCodigos = $item->trabajos->pluck('codigo_trabajo')->toArray();
            $trabajosLabels = collect($trabajosCodigos)
              ->map(fn($c) => isset($trabajosMap[$c]) ? $t($trabajosMap[$c]['label']) : $c)
              ->implode('; ');
          @endphp
          <tr>
            <td style="text-align:center;font-weight:bold;">{{ $i + 1 }}</td>
            <td>{{ $item->producto?->nombre ?? '—' }}</td>
            @foreach ($itemFields as $field)
              <td>
                @php
                  $val = $campos[$field['key']] ?? '';
                  if ($field['type'] === 'boolean') {
                    $val = $val ? ($lang === 'es' ? 'Sí' : 'Yes') : ($lang === 'es' ? 'No' : 'No');
                  } elseif ($field['type'] === 'date' && $val) {
                    try { $val = \Carbon\Carbon::parse($val)->format('d/m/Y'); } catch(\Exception $e) {}
                  } elseif ($field['type'] === 'select' && $val) {
                    $opt = collect($field['options'] ?? [])->firstWhere('value', $val);
                    $val = $opt ? $t($opt['label']) : $val;
                  }
                @endphp
                {{ $val ?: '—' }}
              </td>
            @endforeach
            <td>{{ $trabajosLabels ?: '—' }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- ═══════════════════════════════════════════════ --}}
{{--  DATOS DEL CERTIFICADO                         --}}
{{-- ═══════════════════════════════════════════════ --}}
<div class="section">
  <div class="section-title">
    {{ $lang === 'es' ? 'Datos del certificado' : 'Certificate details' }}
  </div>
  <div class="section-body">
    <div class="cert-data">
      <div class="cert-data-col">
        <div class="data-label">{{ $lang === 'es' ? 'Fecha de emisión / Date of issue' : 'Date of issue / Fecha de emisión' }}</div>
        <div class="data-value">{{ $fechaEmision }}</div>
      </div>
      <div class="cert-data-col">
        <div class="data-label">{{ $lang === 'es' ? 'Próximo servicio / Next service' : 'Next service / Próximo servicio' }}</div>
        <div class="data-value">{{ $fechaProximo }}</div>
      </div>
      <div class="cert-data-col">
        <div class="data-label">{{ $lang === 'es' ? 'Inspector / Surveyor' : 'Surveyor / Inspector' }}</div>
        <div class="data-value">{{ $certificado->inspector ?? '—' }}</div>
      </div>
    </div>
    <div style="margin-top:8px;">
      <div class="data-label">{{ $lang === 'es' ? 'Empresa certificadora / Certifying company' : 'Certifying company / Empresa certificadora' }}</div>
      <div class="data-value">{{ $certificado->empresa_certificadora ?? 'Uruguayan Marine Safety Ltd.' }}</div>
    </div>
    <div style="margin-top:8px;">
      <div class="data-label">{{ $lang === 'es' ? 'Recomendaciones / Recommendations' : 'Recommendations / Recomendaciones' }}</div>
      <div class="data-value" style="font-weight:normal;">{{ $certificado->recomendaciones ?? 'NIL' }}</div>
    </div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════ --}}
{{--  NOTA DE LUCES (condicional)                   --}}
{{-- ═══════════════════════════════════════════════ --}}
@if ($tieneNotaLuces && $notaLucesTexto)
  <div class="nota-luces">
    <strong>⚠</strong> {{ $notaLucesTexto }}
  </div>
@endif

{{-- ═══════════════════════════════════════════════ --}}
{{--  TEXTO LEGAL                                   --}}
{{-- ═══════════════════════════════════════════════ --}}
@foreach ($textosLegales as $texto)
  <div class="legal">
    <div class="legal-title">{{ $lang === 'es' ? 'Declaración de conformidad / Compliance statement' : 'Compliance statement / Declaración de conformidad' }}</div>
    {{ $texto }}
  </div>
@endforeach

{{-- ═══════════════════════════════════════════════ --}}
{{--  FIRMA                                         --}}
{{-- ═══════════════════════════════════════════════ --}}
<div class="firma">
  <div class="firma-col">
    <div style="height:32px;"></div>
    <div class="firma-line">
      {{ $certificado->inspector ?? '____________________________' }}<br>
      <span style="font-size:6.5pt;color:#8a9ab0;">
        {{ $lang === 'es' ? 'Inspector autorizado / Authorized surveyor' : 'Authorized surveyor / Inspector autorizado' }}
      </span>
    </div>
  </div>
  <div class="firma-col">
    <div style="height:32px;"></div>
    <div class="firma-line">
      {{ $certificado->empresa_certificadora ?? 'Uruguayan Marine Safety Ltd.' }}<br>
      <span style="font-size:6.5pt;color:#8a9ab0;">
        {{ $lang === 'es' ? 'Empresa certificadora / Certifying company' : 'Certifying company / Empresa certificadora' }}
      </span>
    </div>
  </div>
</div>

{{-- ═══════════════════════════════════════════════ --}}
{{--  FOOTER                                        --}}
{{-- ═══════════════════════════════════════════════ --}}
<div class="footer">
  <div class="footer-left">
    {{ $tipo->normativa_aplicable ?? '' }}
  </div>
  <div class="footer-right">
    {{ $lang === 'es' ? 'Emitido' : 'Issued' }}: {{ $fechaEmision }}
    &nbsp;·&nbsp;
    {{ $certificado->numero_certificado ?? '' }}
  </div>
</div>

</body>
</html>
```

- [ ] **Step 2: Verificar que el directorio existe**

```powershell
ls C:\wamp64\www\SGC-UMS\backend\resources\views\pdf\
```

Debe mostrar `certificado.blade.php`.

- [ ] **Step 3: Commit**

```powershell
git -C C:\wamp64\www\SGC-UMS add backend/resources/views/pdf/certificado.blade.php
git -C C:\wamp64\www\SGC-UMS commit -m "feat(backend): add PDF Blade template for certificado"
```

---

## Task 4: Método pdf() en CertificadoController + ruta

**Files:**
- Modify: `C:\wamp64\www\SGC-UMS\backend\app\Http\Controllers\CertificadoController.php`
- Modify: `C:\wamp64\www\SGC-UMS\backend\routes\api.php`

- [ ] **Step 1: Agregar use de Pdf facade y método pdf() en CertificadoController**

Al inicio del archivo, agregar el import (después de `use Illuminate\Http\Request;`):

```php
use Barryvdh\DomPDF\Facade\Pdf;
```

Al final de la clase (antes del `}` de cierre), agregar el método:

```php
public function pdf(Certificado $certificado, Request $request): \Illuminate\Http\Response
{
    $certificado->load(['buque', 'tipo', 'items.producto', 'items.trabajos']);

    $plantilla = is_array($certificado->tipo->plantilla)
        ? $certificado->tipo->plantilla
        : json_decode($certificado->tipo->plantilla, true) ?? [];

    $pdf = Pdf::loadView('pdf.certificado', [
        'certificado' => $certificado,
        'plantilla'   => $plantilla,
    ])->setPaper('a4', 'portrait');

    $filename = ($certificado->numero_certificado ?? 'certificado') . '.pdf';

    if ($request->boolean('download')) {
        return $pdf->download($filename);
    }

    return $pdf->stream($filename);
}
```

- [ ] **Step 2: Agregar ruta en api.php**

En `routes/api.php`, dentro del grupo `auth:sanctum`, agregar esta ruta **antes** de `Route::apiResource('certificados', ...)`:

```php
Route::get('certificados/{certificado}/pdf',
    [CertificadoController::class, 'pdf'])
    ->middleware('token.from.query');
```

La sección de certificados debe quedar:

```php
// Certificaciones (wizard): reservar número + crear borrador + listado/detalle
Route::post('certificados/reservar-numero', [CertificadoController::class, 'reservarNumero']);
Route::post('certificados/liberar-numero', [CertificadoController::class, 'liberarNumero']);
Route::get('certificados/{certificado}/pdf',
    [CertificadoController::class, 'pdf'])
    ->middleware('token.from.query');
Route::apiResource('certificados', CertificadoController::class)
    ->only(['index', 'store', 'show'])
    ->parameters(['certificados' => 'certificado']);
```

- [ ] **Step 3: Verificar que la ruta aparece**

```powershell
cd C:\wamp64\www\SGC-UMS\backend
php artisan route:list --path=certificados
```

Debe aparecer `GET api/certificados/{certificado}/pdf`.

- [ ] **Step 4: Commit**

```powershell
git -C C:\wamp64\www\SGC-UMS add backend/app/Http/Controllers/CertificadoController.php backend/routes/api.php
git -C C:\wamp64\www\SGC-UMS commit -m "feat(backend): add GET /api/certificados/{id}/pdf endpoint"
```

---

## Task 5: Frontend — pdfUrl() en ApiService y botones en wizard

**Files:**
- Modify: `C:\wamp64\www\SGC-UMS\frontend\src\app\core\api.service.ts`
- Modify: `C:\wamp64\www\SGC-UMS\frontend\src\app\features\certificacion\wizard.ts`
- Modify: `C:\wamp64\www\SGC-UMS\frontend\src\app\features\certificacion\wizard.html`

- [ ] **Step 1: Agregar pdfUrl() en ApiService**

Agregar el import de `AuthService` y el método `pdfUrl` en `api.service.ts`. El archivo completo debe quedar:

```typescript
import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { AuthService } from './auth.service';

@Injectable({ providedIn: 'root' })
export class ApiService {
  private readonly http = inject(HttpClient);
  private readonly auth = inject(AuthService);
  private readonly base = '/api';

  list<T>(resource: string): Observable<T[]> {
    return this.http.get<T[]>(`${this.base}/${resource}`);
  }

  create<T>(resource: string, body: Partial<T>): Observable<T> {
    return this.http.post<T>(`${this.base}/${resource}`, body);
  }

  post<T>(resource: string, body: unknown): Observable<T> {
    return this.http.post<T>(`${this.base}/${resource}`, body);
  }

  update<T>(resource: string, id: number | string, body: Partial<T>): Observable<T> {
    return this.http.put<T>(`${this.base}/${resource}/${id}`, body);
  }

  remove(resource: string, id: number | string): Observable<void> {
    return this.http.delete<void>(`${this.base}/${resource}/${id}`);
  }

  pdfUrl(certificadoId: number, download = false): string {
    const token = this.auth.token ?? '';
    const base = `${this.base}/certificados/${certificadoId}/pdf`;
    return `${base}?token=${encodeURIComponent(token)}${download ? '&download=1' : ''}`;
  }
}
```

- [ ] **Step 2: Exponer savedId en wizard.ts**

Agregar `readonly savedId = signal<number | null>(null);` y setearlo en el callback de éxito. También inyectar `ApiService`. El archivo completo:

```typescript
import { Component, OnDestroy, inject, signal } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { WizardStore } from './wizard-store';
import { PasoTipo } from './pasos/paso-tipo';
import { PasoBuque } from './pasos/paso-buque';
import { PasoItems } from './pasos/paso-items';
import { PasoDatos } from './pasos/paso-datos';
import { PasoRevision } from './pasos/paso-revision';
import { ApiService } from '../../core/api.service';

@Component({
  selector: 'app-wizard',
  imports: [RouterLink, PasoTipo, PasoBuque, PasoItems, PasoDatos, PasoRevision],
  providers: [WizardStore],
  templateUrl: './wizard.html',
})
export class Wizard implements OnDestroy {
  readonly store = inject(WizardStore);
  readonly api = inject(ApiService);
  private readonly router = inject(Router);

  readonly savedOk = signal(false);
  readonly savedNumero = signal<string>('');
  readonly savedId = signal<number | null>(null);

  guardar(): void {
    this.store.guardarBorrador().subscribe({
      next: (cert: any) => {
        this.store.saving.set(false);
        this.store.marcarConcretado();
        this.savedNumero.set(cert?.numero_certificado || `#${cert?.id_certificado ?? ''}`);
        this.savedId.set(cert?.id_certificado ?? null);
        this.savedOk.set(true);
      },
      error: (err) => {
        this.store.saving.set(false);
        this.store.error.set(err?.error?.message ?? 'No se pudo guardar el certificado.');
      },
    });
  }

  cancelar(): void {
    this.router.navigate(['/']);
  }

  cargarOtra(): void {
    this.store.reset();
    this.savedOk.set(false);
    this.savedId.set(null);
  }

  ngOnDestroy(): void {
    this.store.liberarSiPendiente();
  }
}
```

- [ ] **Step 3: Agregar botones Ver PDF / Descargar en wizard.html**

Reemplazar el bloque de pantalla de éxito (la sección `@if (savedOk())`) con:

```html
@if (savedOk()) {
  <!-- Pantalla de éxito -->
  <div class="max-w-xl mx-auto text-center py-16">
    <div class="h-14 w-14 rounded-full bg-ums-success/10 text-ums-success flex items-center justify-center mx-auto">
      <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="m5 12 5 5 9-11" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
    </div>
    <h1 class="text-2xl font-bold text-ums-on-surface mt-4">Certificado guardado en borrador</h1>
    <p class="text-sm text-ums-on-surface-variant mt-1">
      {{ savedNumero() ? 'Certificado ' + savedNumero() : 'El certificado' }} se guardó correctamente.
    </p>

    <!-- Botones PDF -->
    @if (savedId()) {
      <div class="flex items-center justify-center gap-3 mt-5">
        <a [href]="api.pdfUrl(savedId()!)" target="_blank"
          class="inline-flex items-center gap-2 rounded bg-ums-primary px-4 py-2.5 text-sm font-semibold text-ums-on-primary hover:bg-ums-primary-container transition">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" stroke-linecap="round"/>
            <circle cx="12" cy="12" r="3"/>
          </svg>
          Ver certificado
        </a>
        <a [href]="api.pdfUrl(savedId()!, true)" target="_blank"
          class="inline-flex items-center gap-2 rounded border border-ums-outline px-4 py-2.5 text-sm font-semibold text-ums-on-surface hover:bg-ums-surface-container transition">
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 3v12m0 0-4-4m4 4 4-4M3 17v2a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          Descargar PDF
        </a>
      </div>
    }

    <div class="flex items-center justify-center gap-3 mt-4">
      <a routerLink="/"
        class="rounded px-4 py-2.5 text-sm font-semibold text-ums-on-surface-variant hover:bg-ums-surface-container transition">
        Volver al inicio
      </a>
      <button type="button" (click)="cargarOtra()"
        class="rounded bg-ums-secondary px-4 py-2.5 text-sm font-semibold text-ums-on-secondary hover:bg-ums-secondary-container transition">
        Cargar otra certificación
      </button>
    </div>
  </div>
}
```

- [ ] **Step 4: Commit**

```powershell
git -C C:\wamp64\www\SGC-UMS add frontend/src/app/core/api.service.ts frontend/src/app/features/certificacion/wizard.ts frontend/src/app/features/certificacion/wizard.html
git -C C:\wamp64\www\SGC-UMS commit -m "feat(frontend): add Ver/Descargar PDF buttons after certification save"
```

---

## Task 6: Levantar servidores y verificar

- [ ] **Step 1: Levantar backend**

```powershell
cd C:\wamp64\www\SGC-UMS\backend
php artisan serve --port=8000
```

Dejar corriendo en background.

- [ ] **Step 2: Levantar frontend**

```powershell
cd C:\wamp64\www\SGC-UMS\frontend
npm start
```

Dejar corriendo en background.

- [ ] **Step 3: Verificar endpoint PDF via curl**

Primero obtener un token:
```powershell
$res = Invoke-RestMethod -Method POST -Uri "http://localhost:8000/api/login" -ContentType "application/json" -Body '{"email":"admin@sgc-ums.com","password":"password"}'
$token = $res.token
```

Luego verificar que el PDF responde (reemplazar `{id}` por un id de certificado existente):
```powershell
Invoke-WebRequest -Uri "http://localhost:8000/api/certificados/1/pdf?token=$token" -OutFile "test.pdf"
ls test.pdf
```

Esperado: archivo `test.pdf` de varios KB.

- [ ] **Step 4: Abrir el frontend y completar un certificado de punta a punta**

Ir a `http://localhost:4200`, loguearse, crear un certificado nuevo, y en la pantalla de éxito hacer click en "Ver certificado". Debe abrirse el PDF en una nueva pestaña del browser con el layout correcto.
