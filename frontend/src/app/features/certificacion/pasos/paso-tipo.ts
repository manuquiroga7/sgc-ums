import { Component, OnInit, inject, signal } from '@angular/core';
import { ApiService } from '../../../core/api.service';
import { TipoCertificado } from '../certificacion.models';
import { WizardStore } from '../wizard-store';

@Component({
  selector: 'app-paso-tipo',
  templateUrl: './paso-tipo.html',
})
export class PasoTipo implements OnInit {
  private readonly api = inject(ApiService);
  readonly store = inject(WizardStore);

  readonly tipos = signal<TipoCertificado[]>([]);
  readonly loading = signal(false);

  ngOnInit(): void {
    this.loading.set(true);
    this.api.list<TipoCertificado>('tipos-certificado').subscribe({
      next: (data) => {
        this.tipos.set(data);
        this.loading.set(false);
      },
      error: () => this.loading.set(false),
    });
  }

  seleccionar(t: TipoCertificado): void {
    if (t.plantilla) {
      this.store.setTipo(t);
    }
  }

  seleccionarYAvanzar(t: TipoCertificado): void {
    this.seleccionar(t);
    if (t.plantilla) {
      this.store.next();
    }
  }
}
