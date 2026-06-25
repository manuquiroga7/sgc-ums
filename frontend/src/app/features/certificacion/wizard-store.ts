import { Injectable, computed, inject, signal } from '@angular/core';
import { ApiService } from '../../core/api.service';
import {
  Buque,
  Plantilla,
  TipoCertificado,
  WizardDatos,
  WizardItem,
} from './certificacion.models';

const STEPS = ['Tipo', 'Buque', 'Ítems', 'Datos', 'Revisión'] as const;

function hoyISO(): string {
  return new Date().toISOString().slice(0, 10);
}

function sumarMeses(fechaISO: string, meses: number): string {
  const d = new Date(fechaISO + 'T00:00:00');
  d.setMonth(d.getMonth() + meses);
  return d.toISOString().slice(0, 10);
}

/**
 * Estado del wizard de certificación. Se provee a nivel del componente Wizard,
 * por lo que cada vez que se entra al wizard el estado arranca limpio.
 */
@Injectable()
export class WizardStore {
  private readonly api = inject(ApiService);

  readonly steps = STEPS;
  readonly step = signal(0);
  readonly maxStep = signal(0);

  readonly tipo = signal<TipoCertificado | null>(null);
  readonly buque = signal<Buque | null>(null);
  readonly items = signal<WizardItem[]>([]);
  readonly datos = signal<WizardDatos>({
    numero_certificado: '',
    fecha_emision: hoyISO(),
    fecha_proximo_servicio: sumarMeses(hoyISO(), 12),
    inspector: '',
    recomendaciones: 'NIL',
    idioma: 'es',
  });

  readonly saving = signal(false);
  readonly error = signal<string | null>(null);

  /** Código de la variante elegida (tipos con variantes, p.ej. Inmersión A1/A3). */
  readonly variante = signal<string>('');

  readonly plantilla = computed<Plantilla | null>(() => this.tipo()?.plantilla ?? null);

  /** Intervalo de meses vigente: el de la variante elegida o el de la plantilla. */
  private intervaloActual(): number {
    const pl = this.plantilla();
    const v = pl?.variantes?.find((x) => x.codigo === this.variante());
    return v?.intervalo_meses ?? pl?.intervalo_meses ?? 12;
  }

  // ───── Navegación ─────
  goTo(i: number): void {
    if (i <= this.maxStep()) {
      this.step.set(i);
    }
  }

  next(): void {
    if (this.step() < STEPS.length - 1 && this.canProceed()) {
      // Al dejar el paso Tipo se reserva el número (numeración por tipo).
      if (this.step() === 0) {
        this.asegurarReserva();
      }
      const n = this.step() + 1;
      this.step.set(n);
      this.maxStep.set(Math.max(this.maxStep(), n));
    }
  }

  prev(): void {
    if (this.step() > 0) {
      this.step.set(this.step() - 1);
    }
  }

  canProceed(): boolean {
    switch (this.step()) {
      case 0:
        return !!this.tipo()?.plantilla;
      case 1:
        return !!this.buque();
      case 2:
        return (
          this.items().length > 0 &&
          this.items().every((it) => !!String(it.campos['numero_serie'] ?? '').trim())
        );
      default:
        return true;
    }
  }

  // ───── Mutaciones ─────
  setTipo(t: TipoCertificado): void {
    this.tipo.set(t);
    // Variante por defecto: la primera, si el tipo tiene variantes.
    this.variante.set(t.plantilla?.variantes?.[0]?.codigo ?? '');
    this.datos.update((d) => ({
      ...d,
      fecha_proximo_servicio: sumarMeses(d.fecha_emision, this.intervaloActual()),
    }));
    // Reinicia ítems al cambiar de tipo (cambia el esquema).
    this.items.set([]);
  }

  setVariante(codigo: string): void {
    this.variante.set(codigo);
    this.datos.update((d) => ({
      ...d,
      fecha_proximo_servicio: sumarMeses(d.fecha_emision, this.intervaloActual()),
    }));
  }

  setBuque(b: Buque): void {
    this.buque.set(b);
  }

  addItem(): void {
    const campos: Record<string, unknown> = {};
    for (const f of this.plantilla()?.item_fields ?? []) {
      // El producto_ref se guarda en id_producto, no en campos.
      if (f.type === 'producto_ref') continue;
      campos[f.key] = f.type === 'number' ? null : '';
    }
    this.items.update((arr) => [...arr, { id_producto: null, campos, trabajos: [] }]);
  }

  removeItem(index: number): void {
    this.items.update((arr) => arr.filter((_, i) => i !== index));
  }

  /** Agrega un ítem copiando los datos del primero (campos, trabajos y producto). */
  duplicarPrimero(): void {
    const items = this.items();
    if (items.length === 0) {
      this.addItem();
      return;
    }
    const base = items[0];
    const copia: WizardItem = {
      id_producto: base.id_producto,
      campos: { ...base.campos },
      trabajos: [...base.trabajos],
    };
    this.items.update((arr) => [...arr, copia]);
  }

  patchDatos(patch: Partial<WizardDatos>): void {
    this.datos.update((d) => {
      const next = { ...d, ...patch };
      // Si cambia la fecha de emisión, recalcula el próximo servicio.
      if (patch.fecha_emision) {
        next.fecha_proximo_servicio = sumarMeses(patch.fecha_emision, this.intervaloActual());
      }
      return next;
    });
  }

  // ───── Numeración (reserva / liberación) ─────
  private reservadoParaTipo: number | null = null;
  private committed = false;

  /** Reserva un número para el tipo actual; si cambió de tipo, libera el anterior. */
  private asegurarReserva(): void {
    const t = this.tipo();
    if (!t) return;
    if (this.reservadoParaTipo === t.id_tipo && this.datos().numero_certificado) return;

    const anterior = this.datos().numero_certificado;
    if (anterior) this.liberar(anterior);

    this.reservadoParaTipo = t.id_tipo;
    this.patchDatos({ numero_certificado: '' });
    this.api
      .post<{ numero_certificado: string }>('certificados/reservar-numero', { id_tipo: t.id_tipo })
      .subscribe({
        next: (res) => this.patchDatos({ numero_certificado: res.numero_certificado }),
        error: () => {},
      });
  }

  private liberar(numero: string): void {
    this.api.post('certificados/liberar-numero', { numero_certificado: numero }).subscribe({ error: () => {} });
  }

  /** Marca el número como concretado (al guardar): ya no se libera. */
  marcarConcretado(): void {
    this.committed = true;
  }

  /** Libera el número reservado si la certificación no se completó (cancelar/abandonar). */
  liberarSiPendiente(): void {
    const numero = this.datos().numero_certificado;
    if (numero && !this.committed) {
      this.liberar(numero);
    }
  }

  reset(): void {
    this.reservadoParaTipo = null;
    this.committed = false;
    this.step.set(0);
    this.maxStep.set(0);
    this.tipo.set(null);
    this.variante.set('');
    this.buque.set(null);
    this.items.set([]);
    this.datos.set({
      numero_certificado: '',
      fecha_emision: hoyISO(),
      fecha_proximo_servicio: sumarMeses(hoyISO(), 12),
      inspector: '',
      recomendaciones: 'NIL',
      idioma: 'es',
    });
    this.error.set(null);
  }

  // ───── Guardar ─────
  guardarBorrador() {
    this.saving.set(true);
    this.error.set(null);

    const payload = {
      id_buque: this.buque()!.id_buque,
      id_tipo: this.tipo()!.id_tipo,
      ...this.datos(),
      variante: this.variante() || null,
      estado: 'borrador',
      items: this.items().map((it) => ({
        id_producto: it.id_producto,
        campos: it.campos,
        trabajos: it.trabajos,
      })),
    };

    return this.api.create('certificados', payload);
  }
}
