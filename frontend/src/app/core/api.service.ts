import { Injectable, inject } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';

/**
 * Servicio CRUD genérico contra la API REST de Laravel.
 * `resource` es el segmento de la ruta, p.ej. 'buques', 'productos'.
 */
@Injectable({ providedIn: 'root' })
export class ApiService {
  private readonly http = inject(HttpClient);
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
}
