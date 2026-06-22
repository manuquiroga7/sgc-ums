import { Component, computed, inject } from '@angular/core';
import { ApiService } from '../../../core/api.service';
import { Producto } from '../certificacion.models';
import { WizardStore } from '../wizard-store';
import { signal } from '@angular/core';

@Component({
  selector: 'app-paso-revision',
  templateUrl: './paso-revision.html',
})
export class PasoRevision {
  private readonly api = inject(ApiService);
  readonly store = inject(WizardStore);

  private readonly productos = signal<Producto[]>([]);

  constructor() {
    this.api.list<Producto>('productos').subscribe((data) => this.productos.set(data));
  }

  readonly textoLegal = computed(() => {
    const pl = this.store.plantilla();
    const idioma = this.store.datos().idioma;
    const t = pl?.textos_legales?.[0]?.texto;
    return t ? t[idioma] : '';
  });

  productoNombre(id: number | null): string {
    if (id == null) return '—';
    return this.productos().find((p) => p.id_producto === id)?.nombre ?? `#${id}`;
  }
}
