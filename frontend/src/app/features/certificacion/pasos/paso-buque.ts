import { Component, OnInit, computed, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../../core/api.service';
import { Buque } from '../certificacion.models';
import { WizardStore } from '../wizard-store';

@Component({
  selector: 'app-paso-buque',
  imports: [FormsModule],
  templateUrl: './paso-buque.html',
})
export class PasoBuque implements OnInit {
  private readonly api = inject(ApiService);
  readonly store = inject(WizardStore);

  readonly buques = signal<Buque[]>([]);
  readonly loading = signal(false);
  readonly filtro = signal('');

  readonly showForm = signal(false);
  readonly saving = signal(false);
  readonly error = signal<string | null>(null);
  nuevo: Partial<Buque> = {};

  readonly filtrados = computed(() => {
    const q = this.filtro().toLowerCase().trim();
    const list = this.buques();
    if (!q) return list;
    return list.filter(
      (b) =>
        b.nombre.toLowerCase().includes(q) ||
        (b.numero_imo ?? '').toLowerCase().includes(q) ||
        (b.bandera ?? '').toLowerCase().includes(q),
    );
  });

  ngOnInit(): void {
    this.load();
  }

  private load(): void {
    this.loading.set(true);
    this.api.list<Buque>('buques').subscribe({
      next: (data) => {
        this.buques.set(data);
        this.loading.set(false);
      },
      error: () => this.loading.set(false),
    });
  }

  seleccionar(b: Buque): void {
    this.store.setBuque(b);
  }

  abrirNuevo(): void {
    this.nuevo = { activo: true } as Partial<Buque>;
    this.error.set(null);
    this.showForm.set(true);
  }

  guardarNuevo(): void {
    this.saving.set(true);
    this.error.set(null);
    this.api.create<Buque>('buques', this.nuevo).subscribe({
      next: (b) => {
        this.saving.set(false);
        this.showForm.set(false);
        this.buques.update((arr) => [b, ...arr]);
        this.store.setBuque(b);
      },
      error: (err) => {
        this.saving.set(false);
        this.error.set(err?.error?.message ?? 'No se pudo registrar el buque.');
      },
    });
  }
}
