# Módulo de Mantenimiento — SIGEM

> Sistema de Gestión de Mantenimiento para flota vehicular minera.  
> Stack: Laravel 11 + Filament v3 + DomPDF + Livewire

---

## Índice

1. [Visión general](#1-visión-general)
2. [Estructura de base de datos](#2-estructura-de-base-de-datos)
3. [Modelos y lógica de negocio](#3-modelos-y-lógica-de-negocio)
4. [Enum: MillageItems](#4-enum-millageitems)
5. [Interfaz de usuario (Filament Resources)](#5-interfaz-de-usuario-filament-resources)
6. [Componente Livewire: MantenaceTable](#6-componente-livewire-mantenacetable)
7. [Columna personalizada: BrakePadProgress](#7-columna-personalizada-brakepadadprogress)
8. [Reportes PDF](#8-reportes-pdf)
9. [Permisos y políticas](#9-permisos-y-políticas)
10. [Rutas del módulo](#10-rutas-del-módulo)
11. [Problemas y observaciones](#11-problemas-y-observaciones)

---

## 1. Visión general

El módulo de mantenimiento registra y gestiona los servicios de mantenimiento aplicados a cada vehículo de la flota. Está compuesto por dos entidades principales:

- **`maintenance_items`** — catálogo de tipos de mantenimiento (ej. "Cambio de aceite", "Revisión de frenos").
- **`maintenances`** — registro individual de cada mantenimiento ejecutado sobre un vehículo, vinculando el ítem, el kilometraje, los costos y el estado de las pastillas de freno.

El módulo genera tres tipos de reportes PDF y expone el historial de mantenimientos embebido dentro de la vista de cada vehículo mediante un componente Livewire.

---

## 2. Estructura de base de datos

### Tabla `maintenance_items`

Catálogo maestro de tipos de mantenimiento. Soporta eliminación suave (`softDeletes`).

| Columna | Tipo | Descripción |
|---|---|---|
| `id` | `bigint` (PK) | Identificador |
| `name` | `string` | Nombre del tipo (ej. "Cambio de aceite") |
| `created_at` | `timestamp` | — |
| `updated_at` | `timestamp` | — |
| `deleted_at` | `timestamp` (nullable) | Soft delete |

### Tabla `maintenances`

Registro principal de cada mantenimiento ejecutado.

| Columna | Tipo | Descripción |
|---|---|---|
| `id` | `bigint` (PK) | Identificador |
| `vehicle_id` | `bigint` (FK) | Vehículo atendido → `vehicles.id` (cascade delete) |
| `maintenance_item_id` | `bigint` (FK) | Tipo de mantenimiento → `maintenance_items.id` (cascade delete) |
| `mileage` | `integer` | Kilometraje al momento del mantenimiento (7500–165000, saltos de 7500 km) |
| `status` | `boolean` | Si el mantenimiento fue realizado (`true`) o no (`false`). Default `false` |
| `Price_material` | `decimal(10,2)` | Costo de materiales en soles (S/.) |
| `workforce` | `decimal(10,2)` | Costo de mano de obra en soles (S/.) |
| `maintenance_cost` | `decimal(10,2)` | Costo total = `Price_material` + `workforce` (calculado en frontend, no hay constraint en DB) |
| `photo` | `string` (nullable) | Ruta a foto del mantenimiento (`storage/maintenance/photos/`) |
| `file` | `string` (nullable) | Ruta a boleta/factura del mantenimiento (`storage/maintenance/files/`) |
| `front_left_brake_pad` | `integer` (nullable) | % de vida útil — pastilla delantera izquierda |
| `front_right_brake_pad` | `integer` (nullable) | % de vida útil — pastilla delantera derecha |
| `rear_left_brake_pad` | `integer` (nullable) | % de vida útil — pastilla trasera izquierda |
| `rear_right_brake_pad` | `integer` (nullable) | % de vida útil — pastilla trasera derecha |
| `brake_pads_checked_at` | `timestamp` (nullable) | Fecha de verificación de pastillas (se asigna automáticamente con `now()` al guardar) |
| `created_at` | `timestamp` | Fecha de creación del registro (usada también como "fecha del mantenimiento") |
| `updated_at` | `timestamp` | — |
| `deleted_at` | `timestamp` (nullable) | Soft delete |

**Relaciones:**

```
vehicles      (1) ──── (N) maintenances
maintenance_items (1) ──── (N) maintenances
```

---

## 3. Modelos y lógica de negocio

### `App\Models\Maintenance`

El modelo incluye tres atributos calculados (accessors) que se usan en la columna de progreso de pastillas:

#### `getAverageBrakePadAttribute()`
Calcula el promedio de los cuatro valores de pastillas. Si alguno es `null`, se trata como `0`.

```php
$values = [
    $this->front_left_brake_pad  ?? 0,
    $this->front_right_brake_pad ?? 0,
    $this->rear_left_brake_pad   ?? 0,
    $this->rear_right_brake_pad  ?? 0,
];
return round(array_sum($values) / 4, 2);
```

#### `getBrakePadStatusAttribute()`
Clasifica el estado según el promedio:

| Rango | Estado |
|---|---|
| >= 70% | **Bueno** |
| >= 30% y < 70% | **Regular** |
| < 30% | **Malo** |

#### `getBrakePadStatusColorAttribute()`
Retorna el color Filament correspondiente al estado: `success`, `warning` o `danger`.

### `App\Models\MaintenanceItem`

Modelo simple sin lógica adicional. Solo define `$fillable = ['name']` y usa `SoftDeletes`.

---

## 4. Enum: MillageItems

`App\Enum\MillageItems` es un enum PHP backed (`string`) que implementa `HasLabel` de Filament. Define los valores de kilometraje permitidos:

- Rango: **7,500 km** hasta **165,000 km**
- Intervalo: cada **7,500 km**
- Total: **21 valores**

Este enum se usa en los selects del formulario de registro de mantenimiento para garantizar que el kilometraje siempre sea un valor predefinido, evitando entradas libres inconsistentes.

---

## 5. Interfaz de usuario (Filament Resources)

### `MaintenanceResource`

Recurso Filament para la gestión global de mantenimientos (vista administrativa).

**Navegación:** grupo `Mantenimiento`, ícono `heroicon-o-wrench-screwdriver`.

#### Formulario de creación/edición

El formulario se divide en tres secciones:

**Sección "Archivos"**
- `photo`: FileUpload — foto del mantenimiento, guardada en `public/maintenance/photos/`
- `file`: FileUpload — boleta o factura, guardada en `public/maintenance/files/`

**Grid principal (2 columnas)**
- `vehicle_id`: Select con búsqueda — lista todas las placas de vehículos
- `maintenance_item_id`: Select con búsqueda — lista todos los tipos del catálogo
- `mileage`: Select — opciones tomadas del enum `MillageItems`
- `status`: Select deshabilitado (readonly), valor por defecto `1` ("Sí"). El estado es siempre "realizado" al crear

**Sección "Pastilla de Freno"**
- Cuatro campos numéricos con prefijo `%`: delantera izquierda, delantera derecha, trasera izquierda, trasera derecha
- `brake_pads_checked_at`: DatePicker deshabilitado, se asigna automáticamente con la fecha actual

**Sección "Costos"** (3 columnas)
- `Price_material`: Input numérico con prefijo `S/.` — reactivo
- `workforce`: Input numérico con prefijo `S/.` — reactivo
- `maintenance_cost`: Input numérico, se actualiza automáticamente vía `afterStateUpdated` cuando cambian los dos anteriores:
  ```php
  $set('maintenance_cost', floatval($state) + floatval($get('workforce')));
  ```
  > El cálculo ocurre solo en el frontend. No existe constraint ni trigger en la base de datos que garantice la consistencia de `maintenance_cost`.

#### Tabla de listado

Columnas visibles: placa del vehículo, nombre del ítem, KM, precio material, mano de obra, costo total, estado de pastillas (componente custom).

Columnas ocultas por defecto: `created_at`, `updated_at`, `deleted_at`.

**Filtros:** por vehículo (placa).

**Acciones por fila:** Ver, Editar, Eliminar (soft), Eliminar permanente, Restaurar.

**Acciones bulk:** Eliminar seleccionados.

### `MaintenanceItemResource`

Recurso simple para gestionar el catálogo de tipos de mantenimiento.

**Formulario:** un único campo `name` dentro de una sección "Registrar el Mantenimiento".

**Tabla:** columna `name` con búsqueda y ordenamiento. Soporta soft delete con acciones de restaurar y eliminar permanentemente.

---

## 6. Componente Livewire: MantenaceTable

`App\Livewire\Mantenance\MantenaceTable`

Este componente embebe la tabla de mantenimientos **dentro de la vista de un vehículo específico**. Recibe un `$record` (instancia de `Vehicle`) y filtra los mantenimientos de ese vehículo.

**Diferencias clave respecto a `MaintenanceResource`:**

| Aspecto | MaintenanceResource | MantenaceTable (Livewire) |
|---|---|---|
| Alcance | Todos los mantenimientos | Solo los del vehículo actual |
| Formulario | Página dedicada | Modal (inline, ancho `SevenExtraLarge`) |
| `vehicle_id` | Editable (select) | Deshabilitado y pre-llenado con el vehículo del contexto |
| Columna `status` | TextColumn (oculta) | IconColumn (booleano visible) |
| Acciones de cabecera | Ninguna | Valorizado PDF, Historial PDF, Nuevo Mantenimiento |

**Filtros disponibles:** por kilometraje (`MillageItems`) y por tipo de mantenimiento.

**Acciones bulk:**
- Eliminar seleccionados
- **Generar PDF Valorizado** de los registros seleccionados (usando `Blade::render` + DomPDF, descarga directa con nombre `{placa}-{fecha}.pdf`)

---

## 7. Columna personalizada: BrakePadProgress

`App\Tables\Columns\BrakePadProgress` extiende `ViewColumn` de Filament y renderiza la vista `filament.tables.columns.brake-pad-progress`.

**Lógica:**
- El estado que recibe la vista es el promedio de las 4 pastillas, normalizado al rango [0–100]
- Es **ordenable** mediante SQL raw:
  ```sql
  (COALESCE(front_left_brake_pad,0) + COALESCE(front_right_brake_pad,0) +
   COALESCE(rear_left_brake_pad,0) + COALESCE(rear_right_brake_pad,0)) / 4 {direction}
  ```
- Es **buscable** por texto (`Bueno`, `Regular`, `Malo`) usando un `CASE` SQL equivalente al accessor del modelo

La vista (Blade) no está incluida en el repositorio analizado, pero recibe el porcentaje promedio y presumiblemente renderiza una barra de progreso con color según el estado.

---

## 8. Reportes PDF

El módulo genera cuatro tipos de documentos PDF usando `barryvdh/laravel-dompdf`.

### 8.1 PDF Valorizado por Vehículo

**Controlador:** `ValueMaintenanceVehicleController`  
**Ruta:** `GET /valuemantenacevehicle/{id}`  
**Vista:** `pdf.value-mantenace-vehicle-fast`

Genera un PDF A4 vertical con todos los mantenimientos de un vehículo, incluyendo imágenes (foto y archivo). Incluye varias optimizaciones:

- **Caché de resultado:** el PDF generado se almacena en caché por 2 horas (`vehicle_maintenance_pdf_{id}`). Se puede omitir pasando `?no_cache` como parámetro.
- **Pre-procesamiento de imágenes:** convierte fotos a base64 antes del renderizado. Si la imagen supera 1 MB, la redimensiona a máximo 800×600 px con GD y recomprime al 75% de calidad JPEG.
- **Límite de registros:** máximo 100 mantenimientos por PDF para evitar documentos excesivamente grandes.
- **Caché de imágenes procesadas:** cada imagen procesada se almacena en caché 24 horas usando un hash basado en ruta + `filemtime`.

### 8.2 PDF Historial por Vehículo

**Controlador:** `MaintenanceHistoryController`  
**Ruta:** `GET /maintenacehisrtory/{id}`  
**Vista:** `pdf.history_maintenance`  
**Orientación:** A4 landscape

Genera una matriz de cumplimiento: filas = tipos de mantenimiento, columnas = kilometrajes (de 7,500 a 165,000 km). Cada celda muestra si ese mantenimiento fue realizado (`true`/`false`) en ese kilometraje.

**Construcción de la matriz:**
```php
// Inicializar toda la matriz en false
foreach ($maintenanceitems as $item) {
    foreach ($mileages as $mileage) {
        $maintenanceMatrix[$item->id][$mileage] = false;
    }
}
// Marcar como true los registros existentes
foreach ($maintenances as $maintenance) {
    $maintenanceMatrix[$maintenance->maintenance_item_id][$maintenance->mileage] = true;
}
```

La consulta respeta soft deletes (`whereNull('deleted_at')`).

### 8.3 PDF Valorizado seleccionado (bulk en Livewire)

Generado dentro del `BulkAction` del componente `MantenaceTable`. Recibe una colección de registros seleccionados y genera un único PDF con todos ellos, descargando el archivo directamente como `{placa}-{fecha}.pdf`.

### 8.4 PDF Reporte General de Mantenimientos

**Controlador:** `PrintMaintenanceController`  
**Ruta:** `GET /print-maintenance-vehicle`  
**Vista:** `pdf.print_mantenance`

Genera un reporte filtrado por mes y/o rango de fechas, usando `brake_pads_checked_at` como columna de fecha. Agrupa los resultados por vehículo (`groupBy('vehicle_id')`). Descarga el PDF.

---

## 9. Permisos y políticas

`App\Policies\MaintenancePolicy` implementa control de acceso granular mediante `spatie/laravel-permission` (integrado con Filament Shield).

| Permiso | Método |
|---|---|
| `view_any_maintenance` | Ver listado |
| `view_maintenance` | Ver registro individual |
| `create_maintenance` | Crear |
| `update_maintenance` | Editar |
| `delete_maintenance` | Eliminar (soft) |
| `delete_any_maintenance` | Eliminar en masa |
| `force_delete_maintenance` | Eliminar permanente |
| `force_delete_any_maintenance` | Eliminar permanente en masa |
| `restore_maintenance` | Restaurar soft-deleted |
| `restore_any_maintenance` | Restaurar en masa |
| `replicate_maintenance` | Replicar registro |
| `reorder_maintenance` | Reordenar |

Existe una política equivalente para `MaintenanceItem` (`MaintenanceItemPolicy`).

---

## 10. Rutas del módulo

```php
// Componente Livewire embebido (tabla de mantenimientos)
Route::get('/mantenancetable', MantenaceTable::class)
    ->name('mantenancetable');

// PDF valorizado por vehículo
Route::get('valuemantenacevehicle/{id}', ValueMaintenanceVehicleController::class)
    ->name('valuemantenacevehicle');

// PDF historial de cumplimiento por vehículo
Route::get('maintenacehisrtory/{id}', MaintenanceHistoryController::class)
    ->name('maintenacehistory');

// PDF reporte general con filtros de fecha
Route::get('/print-maintenance-vehicle', PrintMaintenanceController::class)
    ->name('print-maintenance-vehicle');
```

> Ninguna de estas rutas tiene middleware de autenticación explícito en `web.php`. Se asume que el panel Filament protege el acceso mediante su propio middleware.

---

## 11. Problemas y observaciones

### Críticos

**Sin fecha explícita de mantenimiento.** La fecha se infiere de `created_at`, lo que impide registrar mantenimientos con fecha retroactiva o corregir fechas erróneas.

**`maintenance_cost` sin constraint de integridad.** El cálculo `Price_material + workforce` solo ocurre en el frontend (vía `afterStateUpdated`). Si el registro se crea/modifica por API o seeder, el total puede ser incorrecto sin ninguna validación.

**Ruta de historial tiene typo.** La ruta está definida como `maintenacehisrtory/{id}` pero se nombra como `maintenacehistory`. El typo en la URL (`hisrtory`) es inconsistente.

### Menores

**Convención de nombres inconsistente.** El campo `Price_material` usa PascalCase mientras todos los demás campos del sistema usan snake_case.

**Typo en el nombre del componente.** `MantenaceTable` (le falta la `i` de "Maintenance") y la carpeta es `Mantenance` (le falta la `i` también).

**`status` siempre es `true` al crear.** El campo está deshabilitado en el formulario con valor por defecto "Sí". No existe flujo para registrar mantenimientos pendientes (planificados pero no realizados).

**Sin campo de proveedor o mecánico.** No hay registro de quién ejecutó el mantenimiento (taller, mecánico, empresa proveedora).

**`documents` no tiene `softDeletes`.** A diferencia del resto del sistema, la tabla de documentos vehiculares no soporta eliminación suave.

**Caché de PDF sin invalidación.** El PDF valorizado se cachea por 2 horas. Si se edita o agrega un mantenimiento al vehículo, el PDF cacheado seguirá siendo el antiguo hasta que expire.
