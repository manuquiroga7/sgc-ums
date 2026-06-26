# Diseño: Generación de PDF de Certificados

**Fecha:** 2026-06-26  
**Estado:** Aprobado

---

## Objetivo

Permitir visualizar, descargar e imprimir el certificado en formato PDF desde múltiples puntos del sistema: al finalizar el wizard de certificación, y desde cualquier historial o listado futuro que muestre certificados.

---

## Arquitectura

El PDF se genera en el **backend Laravel** bajo demanda usando **DomPDF** (`barryvdh/laravel-dompdf`). No se persiste en disco (para la demo); se genera y sirve en cada request.

```
Frontend                          Backend
   │                                 │
   │  GET /api/certificados/{id}/pdf │
   │ ───────────────────────────────►│
   │                                 │  Carga Certificado + relaciones
   │                                 │  Renderiza Blade → DomPDF
   │◄─────────────────────────────── │
   │  Content-Type: application/pdf  │
```

---

## Endpoint

```
GET /api/certificados/{id}/pdf
GET /api/certificados/{id}/pdf?download=1
```

- **Sin `download`**: `Content-Disposition: inline` → el browser lo muestra en nueva pestaña.
- **Con `download=1`**: `Content-Disposition: attachment` → fuerza descarga.
- **Autenticación**: Sanctum via query param `?token=xxx`, porque `window.open()` no puede enviar headers. Laravel Sanctum soporta esto de forma nativa con `EnsureFrontendRequestsAreStateful` desactivado y usando el guard `sanctum` con `auth:sanctum`.
- **Protección**: mismo middleware `auth:sanctum` que el resto de la API.

---

## Vista Blade: `resources/views/pdf/certificado.blade.php`

### Layout (A4 vertical, CSS inline)

```
┌─────────────────────────────────────────────────────────┐
│  [Logo/Nombre UMS]     TÍTULO DEL CERTIFICADO           │
│                        (bilingüe según idioma)          │
│  Nº: TB-2026-00000001                                   │
├─────────────────────────────────────────────────────────┤
│  BUQUE / VESSEL                                         │
│  Nombre | Bandera | IMO | Call Sign | Tipo              │
├─────────────────────────────────────────────────────────┤
│  TABLA DE ÍTEMS (columnas dinámicas por plantilla)      │
│  N° │ campo1 │ campo2 │ ... │ Trabajos realizados       │
├─────────────────────────────────────────────────────────┤
│  Fecha emisión | Próximo servicio                       │
│  Inspector | Empresa certificadora                      │
│  Recomendaciones: [texto o NIL]                         │
├─────────────────────────────────────────────────────────┤
│  [NOTA LUCES — condicional, solo si plantilla.notas     │
│   contiene key "nota_luces"]                            │
├─────────────────────────────────────────────────────────┤
│  TEXTO LEGAL (de plantilla.textos_legales)              │
├─────────────────────────────────────────────────────────┤
│  Firma: _______________    Sello                        │
│  Inspector autorizado                                   │
└─────────────────────────────────────────────────────────┘
```

### Reglas de rendering

- **Idioma**: si `certificado.idioma === 'en'`, usar la clave `en` de todos los labels bilingües; si no, usar `es`.
- **Tabla de ítems**: las columnas se generan dinámicamente a partir de `plantilla.item_fields`. Cada `item_field` con `type !== 'producto_ref'` es una columna. La columna `producto_ref` muestra el nombre del producto (`item->producto->nombre`).
- **Trabajos realizados**: por ítem, listar los `trabajos` cuyo `codigo` esté en `item->trabajos` (relación `TrabajoRealizado`). Mostrar como lista numerada con el label bilingüe del trabajo.
- **Nota de luces**: mostrar solo si `plantilla.notas` contiene un elemento con `key === 'nota_luces'`. Aplica a Traje de Inmersión y Chalecos/Aros.
- **Texto legal**: iterar `plantilla.textos_legales`. Por ahora renderizar todos los que tengan `condicion === null`. Los que tienen `condicion` con filtro por producto se omiten en la demo (simplificación).
- **Recomendaciones**: siempre visible. Si está vacío o es "NIL", mostrar "NIL / NIL".

---

## Cambios en el Backend

### 1. Instalación de DomPDF
```bash
composer require barryvdh/laravel-dompdf
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

### 2. Nueva ruta en `routes/api.php`
```php
Route::get('/certificados/{certificado}/pdf', [CertificadoController::class, 'pdf']);
```
Dentro del grupo `auth:sanctum`. Agregar soporte de token via query param en `bootstrap/app.php` o en el middleware de Sanctum.

### 3. Nuevo método `pdf()` en `CertificadoController`
```php
public function pdf(Certificado $certificado, Request $request)
{
    $certificado->load(['buque', 'tipo', 'items.producto', 'items.trabajos']);
    $plantilla = $certificado->tipo->plantilla; // array desde JSON

    $pdf = Pdf::loadView('pdf.certificado', compact('certificado', 'plantilla'));
    $pdf->setPaper('a4', 'portrait');

    $filename = $certificado->numero_certificado . '.pdf';
    $disposition = $request->boolean('download') ? 'attachment' : 'inline';

    return $pdf->stream($filename, ['Attachment' => $disposition === 'attachment']);
}
```

### 4. Autenticación por query param
En `config/sanctum.php` o via middleware, habilitar que Sanctum acepte `?token=` en la URL para este endpoint específico.

---

## Cambios en el Frontend

### 1. Método en `api.service.ts`
```typescript
pdfUrl(id: number, download = false): string {
  const token = localStorage.getItem('token');
  const base = `${environment.apiUrl}/certificados/${id}/pdf`;
  return `${base}?token=${token}${download ? '&download=1' : ''}`;
}
```

### 2. Botones en el wizard (paso 4 — post-guardado)
```html
<a [href]="pdfUrl(id)" target="_blank">Ver certificado</a>
<a [href]="pdfUrl(id, true)">Descargar PDF</a>
```

### 3. Preparado para historial futuro
El método `pdfUrl()` en el servicio es suficiente para que cualquier componente futuro (historial, detalle de producto) pueda agregar botones de PDF sin cambios adicionales.

---

## Diseño visual del PDF (demo)

- Fuente: sans-serif estándar (Arial/Helvetica)
- Colores: azul marino para headers de sección (`#1e3a5f`), blanco para texto en headers
- Tabla de ítems: bordes finos, filas alternas con gris claro
- Logo: texto "UMS" en grande como placeholder hasta tener logo real
- Sin firma digital por ahora — línea de firma manual

---

## Simplificaciones para la demo

1. El PDF no se guarda en disco (se genera on-demand).
2. `textos_legales` con `condicion !== null` se omiten (se renderizan todos para simplificar).
3. Sin logo oficial — placeholder de texto.
4. Sin variante en el encabezado del PDF (se puede agregar luego).

---

## Fuera de alcance (fases siguientes)

- Guardado del PDF en storage (S3 / disco local)
- Firma digital / QR de verificación
- Logo oficial de UMS
- Diseño visual refinado según brief de UMS
- Envío por email
