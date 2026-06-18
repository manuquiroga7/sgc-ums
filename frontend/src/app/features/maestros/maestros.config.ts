import { CrudField } from './crud-page';

export interface MaestroConfig {
  title: string;
  subtitle: string;
  resource: string;
  pkField: string;
  fields: CrudField[];
}

export const BUQUES_CONFIG: MaestroConfig = {
  title: 'Buques',
  subtitle: 'Flota registrada para procesos de certificación.',
  resource: 'buques',
  pkField: 'id_buque',
  fields: [
    { key: 'nombre', label: 'Nombre', type: 'text', required: true, placeholder: 'ej. MS Northern Star' },
    { key: 'bandera', label: 'Bandera', type: 'text', placeholder: 'ej. Panamá' },
    { key: 'numero_imo', label: 'N° IMO', type: 'text', placeholder: 'ej. 9123456' },
    { key: 'tipo_buque', label: 'Tipo de buque', type: 'text', placeholder: 'ej. Carguero' },
    { key: 'call_sign', label: 'Call Sign', type: 'text', inList: false },
    { key: 'propietario', label: 'Propietario', type: 'text', inList: false },
    { key: 'activo', label: 'Activo', type: 'boolean' },
  ],
};

export const PRODUCTOS_CONFIG: MaestroConfig = {
  title: 'Productos',
  subtitle: 'Ítems de seguridad sujetos a certificación.',
  resource: 'productos',
  pkField: 'id_producto',
  fields: [
    { key: 'nombre', label: 'Nombre', type: 'text', required: true, placeholder: 'ej. Chaleco salvavidas inflable' },
    { key: 'categoria', label: 'Categoría', type: 'text', placeholder: 'ej. Chaleco, Cilindro, Balsa…' },
    { key: 'subtipo', label: 'Subtipo', type: 'text', placeholder: 'ej. Inflable, SCBA, EEBD…' },
    { key: 'descripcion', label: 'Descripción', type: 'textarea' },
    { key: 'activo', label: 'Activo', type: 'boolean' },
  ],
};

export const TIPOS_CONFIG: MaestroConfig = {
  title: 'Tipos de Certificado',
  subtitle: 'Tipos de inspección, su intervalo y normativa aplicable.',
  resource: 'tipos-certificado',
  pkField: 'id_tipo',
  fields: [
    { key: 'nombre', label: 'Nombre', type: 'text', required: true, placeholder: 'ej. Inspección anual de equipos' },
    { key: 'intervalo_meses', label: 'Intervalo (meses)', type: 'number', placeholder: 'ej. 12' },
    { key: 'normativa_aplicable', label: 'Normativa aplicable', type: 'text', placeholder: 'ej. SOLAS Cap. III' },
    { key: 'descripcion', label: 'Descripción', type: 'textarea' },
  ],
};
