import { Routes } from '@angular/router';
import { authGuard, guestGuard } from './core/auth.guard';
import { BUQUES_CONFIG, PRODUCTOS_CONFIG, TIPOS_CONFIG } from './features/maestros/maestros.config';

export const routes: Routes = [
  {
    path: 'login',
    canActivate: [guestGuard],
    loadComponent: () => import('./features/login/login').then((m) => m.Login),
  },
  {
    path: '',
    canActivate: [authGuard],
    loadComponent: () => import('./layout/shell').then((m) => m.Shell),
    children: [
      {
        path: '',
        loadComponent: () => import('./features/dashboard/dashboard').then((m) => m.Dashboard),
        data: { title: 'Página Principal' },
      },
      {
        path: 'maestros',
        loadComponent: () => import('./features/maestros/maestros').then((m) => m.Maestros),
        data: { title: 'Datos Maestros' },
      },
      {
        path: 'maestros/buques',
        loadComponent: () => import('./features/maestros/crud-page').then((m) => m.CrudPage),
        data: { ...BUQUES_CONFIG },
      },
      {
        path: 'maestros/productos',
        loadComponent: () => import('./features/maestros/crud-page').then((m) => m.CrudPage),
        data: { ...PRODUCTOS_CONFIG },
      },
      {
        path: 'maestros/tipos',
        loadComponent: () => import('./features/maestros/crud-page').then((m) => m.CrudPage),
        data: { ...TIPOS_CONFIG },
      },
    ],
  },
  { path: '**', redirectTo: '' },
];
