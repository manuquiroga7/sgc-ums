import { Injectable, computed, signal } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';

export interface User {
  id: number;
  name: string;
  email: string;
}

interface LoginResponse {
  user: User;
  token: string;
}

const TOKEN_KEY = 'sgc_ums_token';

@Injectable({ providedIn: 'root' })
export class AuthService {
  private readonly apiUrl = '/api';

  private readonly _token = signal<string | null>(localStorage.getItem(TOKEN_KEY));
  private readonly _user = signal<User | null>(null);

  readonly user = this._user.asReadonly();
  readonly isAuthenticated = computed(() => this._token() !== null);

  constructor(private readonly http: HttpClient) {}

  get token(): string | null {
    return this._token();
  }

  login(email: string, password: string): Observable<LoginResponse> {
    return this.http
      .post<LoginResponse>(`${this.apiUrl}/login`, { email, password })
      .pipe(
        tap((res) => {
          localStorage.setItem(TOKEN_KEY, res.token);
          this._token.set(res.token);
          this._user.set(res.user);
        }),
      );
  }

  /** Recupera el usuario autenticado (p.ej. al recargar la página). */
  fetchMe(): Observable<User> {
    return this.http
      .get<User>(`${this.apiUrl}/me`)
      .pipe(tap((user) => this._user.set(user)));
  }

  logout(): void {
    this.http.post(`${this.apiUrl}/logout`, {}).subscribe({
      next: () => this.clearSession(),
      error: () => this.clearSession(),
    });
  }

  private clearSession(): void {
    localStorage.removeItem(TOKEN_KEY);
    this._token.set(null);
    this._user.set(null);
  }
}
