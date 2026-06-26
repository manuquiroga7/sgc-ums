import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { AuthService } from './auth.service';

/**
 * Servicio CRUD genérico contra la API REST de Laravel.
 * `resource` es el segmento de la ruta, p.ej. 'buques', 'productos'.
 */
@Injectable({ providedIn: 'root' })
export class ApiService {
  private readonly http = inject(HttpClient);
  private readonly auth = inject(AuthService);
  private readonly base = '/api';

  list<T>(resource: string): Observable<T[]> {
    return this.http.get<T[]>(`${this.base}/${resource}`);
  }

  create<T>(resource: string, body: Partial<T>): Observable<T> {
    return this.http.post<T>(`${this.base}/${resource}`, body);
  }

  /** POST genérico (body y respuesta independientes). */
  post<T>(resource: string, body: unknown): Observable<T> {
    return this.http.post<T>(`${this.base}/${resource}`, body);
  }

  update<T>(resource: string, id: number | string, body: Partial<T>): Observable<T> {
    return this.http.put<T>(`${this.base}/${resource}/${id}`, body);
  }

  remove(resource: string, id: number | string): Observable<void> {
    return this.http.delete<void>(`${this.base}/${resource}/${id}`);
  }

  /** Construye la URL del PDF con el token Sanctum como query param. */
  pdfUrl(certificadoId: number, download = false): string {
    const token = this.auth.token ?? '';
    const base = `${this.base}/certificados/${certificadoId}/pdf`;
    return `${base}?token=${encodeURIComponent(token)}${download ? '&download=1' : ''}`;
  }
}
