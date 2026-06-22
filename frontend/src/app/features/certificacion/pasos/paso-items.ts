import { Component, OnInit, computed, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../../core/api.service';
import { PlantillaField, Producto, WizardItem } from '../certificacion.models';
import { WizardStore } from '../wizard-store';

@Component({
  selector: 'app-paso-items',
  imports: [FormsModule],
  templateUrl: './paso-items.html',
})
export class PasoItems implements OnInit {
  private readonly api = inject(ApiService);
  readonly store = inject(WizardStore);

  readonly productos = signal<Producto[]>([]);

  readonly fields = computed<PlantillaField[]>(() => this.store.plantilla()?.item_fields ?? []);
  readonly trabajos = computed(() => this.store.plantilla()?.trabajos ?? []);

  // Modal de alta rápida de producto.
  readonly showProductoForm = signal(false);
  readonly savingProducto = signal(false);
  targetItem = -1;
  nuevoProducto: Partial<Producto> = {};

  ngOnInit(): void {
    this.api.list<Producto>('productos').subscribe((data) => this.productos.set(data));
    if (this.store.items().length === 0) {
      this.store.addItem();
    }
  }

  // ───── trabajos ─────
  toggleTrabajo(item: WizardItem, codigo: string): void {
    const has = item.trabajos.includes(codigo);
    item.trabajos = has
      ? item.trabajos.filter((c) => c !== codigo)
      : [...item.trabajos, codigo];
  }

  isTrabajo(item: WizardItem, codigo: string): boolean {
    return item.trabajos.includes(codigo);
  }

  // ───── producto rápido ─────
  abrirNuevoProducto(index: number): void {
    this.targetItem = index;
    this.nuevoProducto = { activo: true } as Partial<Producto>;
    this.showProductoForm.set(true);
  }

  guardarProducto(): void {
    this.savingProducto.set(true);
    this.api.create<Producto>('productos', this.nuevoProducto).subscribe({
      next: (p) => {
        this.savingProducto.set(false);
        this.showProductoForm.set(false);
        this.productos.update((arr) => [...arr, p]);
        const it = this.store.items()[this.targetItem];
        if (it) it.id_producto = p.id_producto;
      },
      error: () => this.savingProducto.set(false),
    });
  }
}
