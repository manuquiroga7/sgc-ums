export interface Bilingue {
  es: string;
  en: string;
}

export interface PlantillaField {
  key: string;
  label: string;
  type: 'text' | 'number' | 'date' | 'boolean' | 'select' | 'producto_ref';
  required?: boolean;
  options?: { value: string; label: string }[];
}

export interface PlantillaTrabajo {
  codigo: string;
  label: Bilingue;
}

export interface Plantilla {
  titulo: Bilingue;
  intervalo_meses: number;
  item_fields: PlantillaField[];
  trabajos: PlantillaTrabajo[];
  textos_legales: { condicion: unknown; texto: Bilingue }[];
  notas: unknown[];
}

export interface TipoCertificado {
  id_tipo: number;
  nombre: string;
  intervalo_meses: number | null;
  normativa_aplicable: string | null;
  descripcion: string | null;
  plantilla: Plantilla | null;
}

export interface Buque {
  id_buque: number;
  nombre: string;
  bandera?: string | null;
  numero_imo?: string | null;
  call_sign?: string | null;
  propietario?: string | null;
  tipo_buque?: string | null;
}

export interface Producto {
  id_producto: number;
  nombre: string;
  categoria?: string | null;
  subtipo?: string | null;
}

/** Una unidad inspeccionada dentro del wizard. */
export interface WizardItem {
  id_producto: number | null;
  /** Valores por clave de campo de la plantilla. */
  campos: Record<string, unknown>;
  /** Códigos de trabajos realizados aplicados. */
  trabajos: string[];
}

export interface WizardDatos {
  numero_certificado: string;
  fecha_emision: string;
  fecha_proximo_servicio: string;
  inspector: string;
  recomendaciones: string;
  idioma: 'es' | 'en';
}
