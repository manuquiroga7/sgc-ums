import { Component, inject } from '@angular/core';
import { RouterLink, RouterLinkActive, RouterOutlet } from '@angular/router';
import { AuthService } from '../core/auth.service';

interface NavItem {
  label: string;
  path: string;
  enabled: boolean;
}

@Component({
  selector: 'app-shell',
  imports: [RouterOutlet, RouterLink, RouterLinkActive],
  templateUrl: './shell.html',
})
export class Shell {
  private readonly auth = inject(AuthService);

  readonly user = this.auth.user;

  // Navegación: por ahora solo el dashboard está activo (scaffold base).
  readonly nav: NavItem[] = [
    { label: 'Panel de Control', path: '/', enabled: true },
    { label: 'Nueva Certificación', path: '/nueva', enabled: false },
    { label: 'Historial', path: '/historial', enabled: false },
    { label: 'Análisis', path: '/analisis', enabled: false },
    { label: 'Configuración', path: '/config', enabled: false },
  ];

  logout(): void {
    this.auth.logout();
  }
}
