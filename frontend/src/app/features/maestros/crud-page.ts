import { Component, Input, OnInit, inject, signal } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { ApiService } from '../../core/api.service';

export interface CrudField {
  key: string;
  label: string;
  type: 'text' | 'number' | 'textarea' | 'boolean';
  required?: boolean;
  placeholder?: string;
  /** Mostrar como columna en la tabla. Por defecto true (excepto textarea). */
  inList?: boolean;
}

type Row = Record<string, unknown>;

@Component({
  selector: 'app-crud-page',
  imports: [FormsModule],
  templateUrl: './crud-page.html',
})
export class CrudPage implements OnInit {
  private readonly api = inject(ApiService);

  // Inputs provistos por la configuración de ruta (withComponentInputBinding).
  @Input({ required: true }) title!: string;
  @Input() subtitle = '';
  @Input({ required: true }) resource!: string;
  @Input({ required: true }) pkField!: string;
  @Input({ required: true }) fields!: CrudField[];
  /** 'table' (por defecto) o 'cards' (bloques con modal de detalle). */
  @Input() layout: 'table' | 'cards' = 'table';

  readonly rows = signal<Row[]>([]);
  readonly loading = signal(false);
  readonly error = signal<string | null>(null);
  readonly saving = signal(false);

  readonly showForm = signal(false);
  readonly editingId = signal<number | string | null>(null);
  model: Row = {};

  /** Fila seleccionada para el modal de detalle (modo cards). */
  readonly detailRow = signal<Row | null>(null);

  ngOnInit(): void {
    this.load();
  }

  get listFields(): CrudField[] {
    return this.fields.filter((f) => f.inList !== false && f.type !== 'textarea');
  }

  load(): void {
    this.loading.set(true);
    this.error.set(null);
    this.api.list<Row>(this.resource).subscribe({
      next: (data) => {
        this.rows.set(data);
        this.loading.set(false);
      },
      error: () => {
        this.error.set('No se pudieron cargar los datos.');
        this.loading.set(false);
      },
    });
  }

  openNew(): void {
    this.editingId.set(null);
    this.model = {};
    for (const f of this.fields) {
      this.model[f.key] = f.type === 'boolean' ? true : '';
    }
    this.showForm.set(true);
  }

  openEdit(row: Row): void {
    this.editingId.set(row[this.pkField] as number | string);
    this.model = { ...row };
    this.showForm.set(true);
  }

  closeForm(): void {
    this.showForm.set(false);
    this.error.set(null);
  }

  openDetail(row: Row): void {
    this.detailRow.set(row);
  }

  closeDetail(): void {
    this.detailRow.set(null);
  }

  /** Editar desde el modal de detalle. */
  editFromDetail(): void {
    const row = this.detailRow();
    if (!row) return;
    this.detailRow.set(null);
    this.openEdit(row);
  }

  /** Eliminar desde el modal de detalle. */
  removeFromDetail(): void {
    const row = this.detailRow();
    if (!row) return;
    this.detailRow.set(null);
    this.remove(row);
  }

  save(): void {
    this.saving.set(true);
    this.error.set(null);

    const body: Row = {};
    for (const f of this.fields) {
      body[f.key] = this.model[f.key];
    }

    const id = this.editingId();
    const req = id == null
      ? this.api.create<Row>(this.resource, body)
      : this.api.update<Row>(this.resource, id, body);

    req.subscribe({
      next: () => {
        this.saving.set(false);
        this.showForm.set(false);
        this.load();
      },
      error: (err) => {
        this.saving.set(false);
        this.error.set(
          err?.error?.message ?? 'No se pudo guardar. Revisá los datos.',
        );
      },
    });
  }

  remove(row: Row): void {
    const label = row['nombre'] ?? 'este registro';
    if (!confirm(`¿Eliminar "${label}"? Esta acción no se puede deshacer.`)) {
      return;
    }
    this.api.remove(this.resource, row[this.pkField] as number | string).subscribe({
      next: () => this.load(),
      error: () => this.error.set('No se pudo eliminar (puede tener registros asociados).'),
    });
  }
}
