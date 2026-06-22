import { Component, inject } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { WizardStore } from '../wizard-store';

@Component({
  selector: 'app-paso-datos',
  imports: [FormsModule],
  templateUrl: './paso-datos.html',
})
export class PasoDatos {
  readonly store = inject(WizardStore);
}
