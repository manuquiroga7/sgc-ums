import { Injectable, signal } from '@angular/core';

@Injectable({ providedIn: 'root' })
export class PageTitleService {
  readonly title = signal('Página Principal');

  set(value: string): void {
    this.title.set(value);
  }
}
