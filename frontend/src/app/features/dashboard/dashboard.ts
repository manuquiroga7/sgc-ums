import { Component, OnInit, inject, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { AuthService } from '../../core/auth.service';

interface Health {
  status: string;
  app: string;
  time: string;
}

@Component({
  selector: 'app-dashboard',
  templateUrl: './dashboard.html',
})
export class Dashboard implements OnInit {
  private readonly http = inject(HttpClient);
  private readonly auth = inject(AuthService);

  readonly user = this.auth.user;
  readonly health = signal<Health | null>(null);
  readonly apiOk = signal<boolean | null>(null);
  readonly meOk = signal<boolean | null>(null);

  ngOnInit(): void {
    // Healthcheck público.
    this.http.get<Health>('/api/health').subscribe({
      next: (h) => {
        this.health.set(h);
        this.apiOk.set(h.status === 'ok');
      },
      error: () => this.apiOk.set(false),
    });

    // Ruta protegida: confirma que el token funciona end-to-end.
    this.auth.fetchMe().subscribe({
      next: () => this.meOk.set(true),
      error: () => this.meOk.set(false),
    });
  }
}
