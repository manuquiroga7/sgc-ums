<!DOCTYPE html>
<html lang="{{ $certificado->idioma ?? 'es' }}">
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  @page {
    margin-top: 62px;
    margin-bottom: 32px;
    margin-left: 32px;
    margin-right: 32px;
  }

  body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 8.5pt;
    color: #1a1a1a;
    background: #fff;
    margin: 0;
    padding: 0 28px;
  }

  /* ── Variables de marca ── */
  /* Azul UMS: #1B6090 | Naranja UMS: #C8561A */

  /* ── ENCABEZADO FIJO (repite en cada página) ── */
  .page-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 52px;
    background: #fff;
    display: table;
    width: 100%;
    padding: 0 0 0 0;
  }
  .ph-logo {
    display: table-cell;
    width: 130px;
    vertical-align: middle;
    padding-left: 20px;
    padding-top: 8px;
  }
  .ph-logo img { width: 148px; height: auto; }
  .ph-title {
    display: table-cell;
    vertical-align: middle;
    text-align: center;
    padding-top: 8px;
  }
  .ph-title-sub {
    font-size: 6pt;
    color: #9aaab8;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .ph-title-main {
    font-size: 9.5pt;
    font-weight: bold;
    color: #1B6090;
    line-height: 1.2;
  }
  .ph-right {
    display: table-cell;
    vertical-align: middle;
    text-align: right;
    width: 200px;
    padding-right: 20px;
    padding-top: 8px;
  }
  .ph-num-label {
    font-size: 6pt;
    color: #9aaab8;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .ph-num-value {
    font-size: 9.5pt;
    font-weight: bold;
    color: #1B6090;
    font-family: monospace;
  }
  .ph-date {
    font-size: 7pt;
    color: #6b7a8d;
    margin-top: 1px;
  }
  .ph-divider {
    display: table-cell;
    width: 1px;
    vertical-align: middle;
  }
  .ph-divider-inner {
    width: 1px;
    height: 34px;
    background: #dde5ed;
    margin: 0 12px;
  }
  .cert-estado {
    display: inline-block;
    background: #fff4ee;
    border: 1px solid #C8561A;
    color: #C8561A;
    font-size: 6pt;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 1px 6px;
    border-radius: 3px;
    margin-top: 2px;
  }

  /* ── SECCIÓN GENÉRICA ── */
  .section {
    margin: 14px 0 0;
  }
  .section-header {
    display: table;
    width: 100%;
    margin-bottom: 6px;
  }
  .section-label {
    display: table-cell;
    font-size: 6.5pt;
    font-weight: bold;
    color: #1B6090;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    vertical-align: middle;
  }
  .section-line {
    display: table-cell;
    vertical-align: middle;
    padding-left: 8px;
  }
  .section-line-inner {
    height: 1px;
    background: #dde5ed;
  }

  /* ── BUQUE ── */
  .buque-grid {
    display: table;
    width: 100%;
    background: #f7fafd;
    border: 1px solid #dde5ed;
    border-radius: 4px;
  }
  .buque-grid-row {
    display: table-row;
  }
  .buque-field {
    display: table-cell;
    padding: 8px 12px;
    border-right: 1px solid #dde5ed;
    vertical-align: top;
  }
  .buque-field:last-child {
    border-right: none;
  }
  .field-label {
    font-size: 6pt;
    color: #7a8fa3;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    margin-bottom: 2px;
  }
  .field-value {
    font-size: 8.5pt;
    font-weight: bold;
    color: #1a1a1a;
  }
  .field-value.mono {
    font-family: monospace;
    font-size: 8pt;
  }

  /* ── ÍTEMS: layout tarjetas ── */
  .items-wrapper {
    border: 1px solid #dde5ed;
    border-radius: 4px;
    overflow: hidden;
  }
  .item-card {
    padding: 9px 12px;
    border-bottom: 1px solid #eef2f7;
  }
  .item-card:last-child {
    border-bottom: none;
  }
  .item-card-header {
    display: table;
    width: 100%;
    margin-bottom: 6px;
  }
  .item-num {
    display: table-cell;
    width: 22px;
    vertical-align: middle;
  }
  .item-num-badge {
    width: 18px;
    height: 18px;
    background: #1B6090;
    color: #fff;
    font-size: 7pt;
    font-weight: bold;
    text-align: center;
    line-height: 18px;
    border-radius: 50%;
  }
  .item-product {
    display: table-cell;
    vertical-align: middle;
    font-size: 8.5pt;
    font-weight: bold;
    color: #1B6090;
  }
  .item-trabajos-cell {
    display: table-cell;
    vertical-align: middle;
    text-align: right;
    font-size: 7pt;
    color: #6b7a8d;
    width: 240px;
  }
  .item-fields-grid {
    display: table;
    width: 100%;
  }
  .item-fields-row {
    display: table-row;
  }
  .item-field {
    display: table-cell;
    padding: 2px 8px 2px 0;
    vertical-align: top;
    width: 16.66%;
  }

  /* ── DATOS DEL CERTIFICADO ── */
  .datos-grid {
    display: table;
    width: 100%;
    background: #f7fafd;
    border: 1px solid #dde5ed;
    border-radius: 4px;
  }
  .datos-row {
    display: table-row;
  }
  .datos-field {
    display: table-cell;
    padding: 8px 12px;
    border-right: 1px solid #dde5ed;
    vertical-align: top;
  }
  .datos-field:last-child {
    border-right: none;
  }
  .datos-field.full {
    border-top: 1px solid #dde5ed;
  }

  /* ── NOTA DE LUCES ── */
  .nota-luces {
    margin: 14px 0 0;
    background: #fff8f0;
    border-left: 3px solid #C8561A;
    border-radius: 0 3px 3px 0;
    padding: 8px 12px;
    font-size: 7.5pt;
    color: #6b3a10;
    line-height: 1.5;
  }
  .nota-luces strong {
    color: #C8561A;
    display: block;
    font-size: 6.5pt;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 3px;
  }

  /* ── TEXTO LEGAL ── */
  .legal {
    margin: 14px 0 0;
    padding: 10px 12px;
    background: #f7fafd;
    border: 1px solid #dde5ed;
    border-radius: 4px;
    font-size: 7pt;
    color: #4a5568;
    line-height: 1.55;
  }
  .legal-label {
    font-size: 6pt;
    font-weight: bold;
    color: #1B6090;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 5px;
  }

  /* ── FIRMA ── */
  .firma-section {
    display: table;
    width: 100%;
    margin: 18px 0 0;
    padding-right: 40px;
  }
  .firma-col {
    display: table-cell;
    width: 50%;
    padding-right: 24px;
    vertical-align: bottom;
  }
  .firma-space { height: 30px; }
  .firma-line {
    border-top: 1px solid #1B6090;
    padding-top: 5px;
  }
  .firma-name {
    font-size: 8pt;
    font-weight: bold;
    color: #1a1a1a;
  }
  .firma-role {
    font-size: 6.5pt;
    color: #7a8fa3;
    margin-top: 1px;
  }

  /* ── PIE FIJO ── */
  .page-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    height: 28px;
    background: #fff;
  }

  /* ── FOOTER ── */
  .footer {
    margin: 14px 0 0;
    padding-top: 7px;
    border-top: 1px solid #eef2f7;
    display: table;
    width: 100%;
  }
  .footer-left {
    display: table-cell;
    font-size: 6.5pt;
    color: #9aaab8;
    vertical-align: middle;
  }
  .footer-right {
    display: table-cell;
    text-align: right;
    font-size: 6.5pt;
    color: #9aaab8;
    vertical-align: middle;
  }
  .footer-dot {
    color: #C8561A;
    margin: 0 5px;
  }
</style>
</head>
<body>

@php
  $lang        = $certificado->idioma ?? 'es';
  $t           = fn($obj) => is_array($obj) ? ($obj[$lang] ?? $obj['es'] ?? '') : ($obj ?? '');
  $pl          = $plantilla;
  $tipo        = $certificado->tipo;
  $buque       = $certificado->buque;

  $logoPath    = public_path('images/ums-logo.png');

  // Campos de ítems (excluir producto_ref)
  $itemFields  = collect($pl['item_fields'] ?? [])
    ->filter(fn($f) => ($f['type'] ?? '') !== 'producto_ref')
    ->values();

  // Nota de luces
  $notaLuces = collect($pl['notas'] ?? [])->firstWhere('key', 'nota_luces');
  $notaLucesTexto = $notaLuces ? $t($notaLuces['texto'] ?? '') : '';

  // Trabajos map: codigo => label
  $trabajosMap = collect($pl['trabajos'] ?? [])->keyBy('codigo');

  // Textos legales sin condición
  $textosLegales = collect($pl['textos_legales'] ?? [])
    ->filter(fn($tl) => ($tl['condicion'] ?? null) === null)
    ->map(fn($tl) => $t($tl['texto'] ?? ''))
    ->filter()->values();

  $fechaEmision = $certificado->fecha_emision
    ? \Carbon\Carbon::parse($certificado->fecha_emision)->format('d/m/Y') : '—';
  $fechaProximo = $certificado->fecha_proximo_servicio
    ? \Carbon\Carbon::parse($certificado->fecha_proximo_servicio)->format('d/m/Y') : '—';

  $tituloEs = $pl['titulo']['es'] ?? $tipo->nombre ?? 'Certificado';
  $tituloEn = $pl['titulo']['en'] ?? '';

  $labelBuque    = $lang === 'es' ? 'Buque / Vessel'                       : 'Vessel / Buque';
  $labelItems    = $lang === 'es' ? 'Equipos inspeccionados'                : 'Inspected equipment';
  $labelDatos    = $lang === 'es' ? 'Datos del certificado'                 : 'Certificate details';
  $labelLegal    = $lang === 'es' ? 'Declaración de conformidad'            : 'Compliance statement';
  $labelTrabajos = $lang === 'es' ? 'Trabajos realizados / Works performed' : 'Works performed / Trabajos realizados';
@endphp

{{-- ══════════════════════════════════════════════════════════ --}}
{{--  ENCABEZADO FIJO — se repite en todas las páginas      --}}
{{-- ══════════════════════════════════════════════════════════ --}}
<div class="page-header">
  <div class="ph-logo">
    @if (file_exists($logoPath))
      <img src="{{ $logoPath }}" alt="UMS">
    @else
      <span style="font-size:14pt;font-weight:bold;color:#1B6090;letter-spacing:2px;">UMS</span>
    @endif
  </div>

  <div class="ph-divider"><div class="ph-divider-inner"></div></div>

  <div class="ph-title">
    <div class="ph-title-sub">{{ $lang === 'es' ? 'Certificado de Inspección' : 'Inspection Certificate' }}</div>
    <div class="ph-title-main">{{ $tituloEs }}</div>
  </div>

  <div class="ph-divider"><div class="ph-divider-inner"></div></div>

  <div class="ph-right">
    <div class="ph-num-label">{{ $lang === 'es' ? 'N° de certificado' : 'Certificate No.' }}</div>
    <div class="ph-num-value">{{ $certificado->numero_certificado ?? '—' }}</div>
    <div class="ph-date">{{ $fechaEmision }}</div>
    <div><span class="cert-estado">{{ $certificado->estado ?? 'borrador' }}</span></div>
  </div>
</div>

{{-- Espacio para que el contenido no quede debajo del header fijo --}}
<div style="height: 62px;"></div>

{{-- ══════════════════════════════════════ --}}
{{--  BUQUE                                --}}
{{-- ══════════════════════════════════════ --}}
<div class="section">
  <div class="section-header">
    <div class="section-label">{{ $labelBuque }}</div>
  </div>

  <div class="buque-grid">
    <div class="buque-grid-row">
      <div class="buque-field" style="width:30%">
        <div class="field-label">{{ $lang === 'es' ? 'Nombre / Name' : 'Name / Nombre' }}</div>
        <div class="field-value">{{ $buque->nombre ?? '—' }}</div>
      </div>
      <div class="buque-field" style="width:16%">
        <div class="field-label">{{ $lang === 'es' ? 'Bandera / Flag' : 'Flag / Bandera' }}</div>
        <div class="field-value">{{ $buque->bandera ?? '—' }}</div>
      </div>
      <div class="buque-field" style="width:18%">
        <div class="field-label">N° IMO</div>
        <div class="field-value mono">{{ $buque->numero_imo ?? '—' }}</div>
      </div>
      <div class="buque-field" style="width:15%">
        <div class="field-label">Call Sign</div>
        <div class="field-value mono">{{ $buque->call_sign ?? '—' }}</div>
      </div>
      <div class="buque-field">
        <div class="field-label">{{ $lang === 'es' ? 'Propietario / Owner' : 'Owner / Propietario' }}</div>
        <div class="field-value">{{ $buque->propietario ?? '—' }}</div>
      </div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════ --}}
{{--  ÍTEMS: tarjetas                      --}}
{{-- ══════════════════════════════════════ --}}
<div class="section">
  <div class="section-header">
    <div class="section-label">
      {{ $labelItems }}
      <span style="color:#9aaab8;font-weight:normal;margin-left:6px;">
        {{ $certificado->items->count() }} {{ $certificado->items->count() === 1 ? ($lang === 'es' ? 'unidad' : 'unit') : ($lang === 'es' ? 'unidades' : 'units') }}
      </span>
    </div>
  </div>

  <div class="items-wrapper">
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
        $trabajosLabels  = collect($trabajosCodigos)
          ->map(fn($c) => isset($trabajosMap[$c]) ? $t($trabajosMap[$c]['label']) : $c)
          ->implode(' · ');

        // Repartir campos en filas de 4 columnas
        $fieldsChunks = $itemFields->chunk(4);
      @endphp

      <div class="item-card" style="{{ $i % 2 === 1 ? 'background:#fafcfe;' : '' }}">

        {{-- Cabecera: número + producto + resultado + trabajos --}}
        <div class="item-card-header">
          <div class="item-num">
            <div class="item-num-badge">{{ $i + 1 }}</div>
          </div>
          <div class="item-product" style="padding-left:6px;">
            {{ $item->producto?->nombre ?? '—' }}
            @php
              $resultado = $campos['resultado'] ?? null;
            @endphp
            @if ($resultado)
              <span style="margin-left:8px;font-size:7pt;font-weight:normal;
                padding:1px 7px;border-radius:3px;
                background:{{ $resultado === 'OK' ? '#e8f5e9' : '#fdecea' }};
                color:{{ $resultado === 'OK' ? '#2e7d32' : '#c62828' }};
                border:1px solid {{ $resultado === 'OK' ? '#a5d6a7' : '#ef9a9a' }};">
                {{ $resultado }}
              </span>
            @endif
          </div>
        </div>

        {{-- Campos en filas de 4 --}}
        @foreach ($fieldsChunks as $chunk)
          <div class="item-fields-grid" style="margin-top:4px;">
            <div class="item-fields-row">
              @foreach ($chunk as $field)
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
                  if ($field['key'] === 'resultado') { $val = null; }
                @endphp
                @if ($val !== null)
                  <div class="item-field">
                    <div class="field-label">{{ $t($field['label']) }}</div>
                    <div class="field-value" style="font-size:8pt;">{{ $val ?: '—' }}</div>
                  </div>
                @endif
              @endforeach
            </div>
          </div>
        @endforeach

        {{-- Trabajos como campos Sí/No --}}
        @if (count($pl['trabajos'] ?? []) > 0)
          <div style="margin-top:8px;padding-top:7px;border-top:1px solid #eef2f7;">
            <div class="field-label" style="color:#1B6090;font-weight:bold;margin-bottom:5px;text-transform:uppercase;letter-spacing:0.5px;">
              {{ $lang === 'es' ? 'Trabajos realizados' : 'Works performed' }}
            </div>
            <div class="item-fields-grid">
              <div class="item-fields-row">
                @foreach ($pl['trabajos'] as $trabajo)
                  @php $hecho = in_array((string)$trabajo['codigo'], $trabajosCodigos, true); @endphp
                  <div class="item-field">
                    <div class="field-label">{{ $t($trabajo['label']) }}</div>
                    <div class="field-value" style="font-size:8pt;color:{{ $hecho ? '#2e7d32' : '#9aaab8' }};">
                      {{ $hecho ? ($lang === 'es' ? 'Sí' : 'Yes') : ($lang === 'es' ? 'No' : 'No') }}
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        @endif

      </div>
    @endforeach
  </div>
</div>

{{-- ══════════════════════════════════════ --}}
{{--  DATOS DEL CERTIFICADO               --}}
{{-- ══════════════════════════════════════ --}}
<div class="section">
  <div class="section-header">
    <div class="section-label">{{ $labelDatos }}</div>
  </div>

  <div class="datos-grid">
    <div class="datos-row">
      <div class="datos-field" style="width:20%">
        <div class="field-label">{{ $lang === 'es' ? 'Fecha de emisión' : 'Date of issue' }}</div>
        <div class="field-value">{{ $fechaEmision }}</div>
      </div>
      <div class="datos-field" style="width:20%">
        <div class="field-label">{{ $lang === 'es' ? 'Próximo servicio' : 'Next service' }}</div>
        <div class="field-value">{{ $fechaProximo }}</div>
      </div>
      <div class="datos-field" style="width:22%">
        <div class="field-label">{{ $lang === 'es' ? 'Inspector' : 'Surveyor' }}</div>
        <div class="field-value">{{ $certificado->inspector ?? '—' }}</div>
      </div>
      <div class="datos-field">
        <div class="field-label">{{ $lang === 'es' ? 'Empresa certificadora' : 'Certifying company' }}</div>
        <div class="field-value">{{ $certificado->empresa_certificadora ?? 'Uruguayan Marine Safety Ltd.' }}</div>
      </div>
    </div>
    <div class="datos-row">
      <div class="datos-field full" style="border-top:1px solid #dde5ed;" colspan="4">
        <div class="field-label">{{ $lang === 'es' ? 'Recomendaciones / Recommendations' : 'Recommendations / Recomendaciones' }}</div>
        <div class="field-value" style="font-weight:normal;font-size:8pt;margin-top:2px;">{{ $certificado->recomendaciones ?? 'NIL' }}</div>
      </div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════ --}}
{{--  NOTA DE LUCES (condicional)         --}}
{{-- ══════════════════════════════════════ --}}
@if ($notaLucesTexto)
  <div class="nota-luces">
    <strong>{{ $lang === 'es' ? 'NOTA' : 'NOTE' }}</strong>
    {{ $notaLucesTexto }}
  </div>
@endif

{{-- ══════════════════════════════════════ --}}
{{--  TEXTO LEGAL                         --}}
{{-- ══════════════════════════════════════ --}}
@foreach ($textosLegales as $texto)
  <div class="legal">
    <div class="legal-label">{{ $labelLegal }}</div>
    {{ $texto }}
  </div>
@endforeach

{{-- ══════════════════════════════════════ --}}
{{--  FIRMA                               --}}
{{-- ══════════════════════════════════════ --}}
<div class="firma-section">
  <div class="firma-col">
    <div class="firma-space"></div>
    <div class="firma-line">
      <div class="firma-name">{{ $certificado->inspector ?? '' }}</div>
      <div class="firma-role">{{ $lang === 'es' ? 'Inspector autorizado / Authorized surveyor' : 'Authorized surveyor / Inspector autorizado' }}</div>
    </div>
  </div>
  <div class="firma-col">
    <div class="firma-space"></div>
    <div class="firma-line">
      <div class="firma-name">{{ $certificado->empresa_certificadora ?? 'Uruguayan Marine Safety Ltd.' }}</div>
      <div class="firma-role">{{ $lang === 'es' ? 'Empresa certificadora / Certifying company' : 'Certifying company / Empresa certificadora' }}</div>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════ --}}
{{--  FOOTER                              --}}
{{-- ══════════════════════════════════════ --}}
<div class="footer">
  <div class="footer-left">
    {{ $tipo->normativa_aplicable ?? '' }}
  </div>
  <div class="footer-right">
    {{ $lang === 'es' ? 'Emitido' : 'Issued' }}: {{ $fechaEmision }}
    <span class="footer-dot">&bull;</span>
    {{ $certificado->numero_certificado ?? '' }}
    <span class="footer-dot">&bull;</span>
    Uruguayan Marine Safety Ltd.
  </div>
</div>

<div class="page-footer"></div>

</body>
</html>
