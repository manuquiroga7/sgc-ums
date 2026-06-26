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

  // Nota de luces condicional
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
    <div class="cert-num">N&deg; {{ $certificado->numero_certificado ?? '—' }}</div>
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
          <div class="data-label">N&deg; IMO</div>
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
                'numero_serie'      => $item->numero_serie,
                'fabricante'        => $item->fabricante,
                'modelo'            => $item->modelo,
                'fecha_fabricacion' => $item->fecha_fabricacion,
                'aprobacion'        => $item->aprobacion,
                'venc_luz'          => $item->venc_luz,
                'resultado'         => $item->resultado,
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
                  if (($field['type'] ?? '') === 'boolean') {
                    $val = $val ? ($lang === 'es' ? 'Sí' : 'Yes') : ($lang === 'es' ? 'No' : 'No');
                  } elseif (($field['type'] ?? '') === 'date' && $val) {
                    try { $val = \Carbon\Carbon::parse($val)->format('d/m/Y'); } catch(\Exception $e) {}
                  } elseif (($field['type'] ?? '') === 'select' && $val) {
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
    <strong>&#9888;</strong> {{ $notaLucesTexto }}
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
    &nbsp;&middot;&nbsp;
    {{ $certificado->numero_certificado ?? '' }}
  </div>
</div>

</body>
</html>
