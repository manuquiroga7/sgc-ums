import { Component, OnDestroy, inject, signal } from '@angular/core';
import { Router, RouterLink } from '@angular/router';
import { WizardStore } from './wizard-store';
import { PasoTipo } from './pasos/paso-tipo';
import { PasoBuque } from './pasos/paso-buque';
import { PasoItems } from './pasos/paso-items';
import { PasoDatos } from './pasos/paso-datos';
import { PasoRevision } from './pasos/paso-revision';

@Component({
  selector: 'app-wizard',
  imports: [RouterLink, PasoTipo, PasoBuque, PasoItems, PasoDatos, PasoRevision],
  providers: [WizardStore],
  templateUrl: './wizard.html',
})
export class Wizard implements OnDestroy {
  readonly store = inject(WizardStore);
  private readonly router = inject(Router);

  readonly savedOk = signal(false);
  readonly savedNumero = signal<string>('');

  guardar(): void {
    this.store.guardarBorrador().subscribe({
      next: (cert: any) => {
        this.store.saving.set(false);
        this.store.marcarConcretado();
        this.savedNumero.set(cert?.numero_certificado || `#${cert?.id_certificado ?? ''}`);
        this.savedOk.set(true);
      },
      error: (err) => {
        this.store.saving.set(false);
        this.store.error.set(err?.error?.message ?? 'No se pudo guardar el certificado.');
      },
    });
  }

  cancelar(): void {
    // El número se libera en ngOnDestroy (al salir del wizard).
    this.router.navigate(['/']);
  }

  cargarOtra(): void {
    this.store.reset();
    this.savedOk.set(false);
  }

  ngOnDestroy(): void {
    // Si la certificación no se completó, devuelve el número a la bolsa.
    this.store.liberarSiPendiente();
  }
}
