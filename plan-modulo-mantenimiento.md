# Plan: Módulo de Mantenimiento — Mejoras

> **Fecha:** 25 de abril 2026  
> **Proyecto:** SIGEM - Sistema de Gestión de Mantenimiento

---

## Resumen Ejecutivo

El documento `modulo-mantenimiento-sigem.md` describe una arquitectura diferente a la implementación real. Este plan alinea el código con las funcionalidades documentadas.

| Prioridad | Funcionalidad | Estado actual |
|----------|-------------|--------------|
| Alta | Soft Deletes (4 tablas) | ❌ No implementado |
| Alta | Reportes PDF (3 controladores) | ❌ No existen |
| Media | Bug fix Job notificaciones | ⚠️ Bug confirmado |

---

## Fase 1: Soft Deletes

### Tablas a modificar

| # | Tabla | Migración a crear |
|---|------|-----------------|
| 1 | `maintenance_types` | `add_softDeletes_to_maintenance_types_table.php` |
| 2 | `maintenance_orders` | `add_softDeletes_to_maintenance_orders_table.php` |
| 3 | `maintenance_order_parts` | `add_softDeletes_to_maintenance_order_parts_table.php` |
| 4 | `mechanics` | `add_softDeletes_to_mechanics_table.php` |

### Modelos a actualizar

Añadir en cada modelo:

```php
use Illuminate\Database\Eloquent\SoftDeletes;

class Xxx extends Model
{
    use SoftDeletes;

    // ... resto del código
}
```

**Modelos:**
- `App\Models\MaintenanceType`
- `App\Models\MaintenanceOrder`
- `App\Models\MaintenanceOrderPart`
- `App\Models\Mechanic`

> **Nota:** Verificar si `SparePart` necesita soft deletes (el documento indica que no tiene, pero revisar relaciones).

---

## Fase 2: Reportes PDF

### Dependencias

```bash
composer require sarder/pdfstudio
# Después ejecutar para instalar drivers:
php artisan pdf-studio:install
# O instalar todo:
php artisan pdf-studio:install --all
```

**Paquetes incluidos:**
| Driver | Paquete |
|--------|--------|
| Chromium (recomendado para Tailwind) | `spatie/browsershot` |
| Dompdf | `dompdf/dompdf` |
| Manipulación PDF | `setasign/fpdi` |
| Formularios | `mikehaertl/php-pdftk` |
| Barcodes | `picqer/php-barcode-generator` |
| QR codes | `chillerlan/php-qrcode` |

### Controladores a crear

| # | Controlador | Ruta | Descripción |
|---|------------|-----|------------|
| 1 | `ValueMaintenanceVehicleController` | `GET /maintenance/vehicle-pdf/{id}` | PDF valorizado por vehículo con fotos |
| 2 | `MaintenanceHistoryController` | `GET /maintenance/history/{id}` | Matriz de cumplimiento (tipos vs KM) |
| 3 | `PrintMaintenanceController` | `GET /maintenance/print` | Reporte filtrado por fecha |

**Ubicación:** `app/Http/Controllers/Maintenance/`

### Vistas blade

| Vista | Ubicación |
|-------|----------|
| PDF valorizado | `resources/views/pdf/maintenance-vehicle.blade.php` |
| Historial/matriz | `resources/views/pdf/maintenance-history.blade.php` |
| Reporte general | `resources/views/pdf/maintenance-print.blade.php` |

### Rutas

Añadir en `routes/web.php`:

```php
use App\Http\Controllers\Maintenance\ValueMaintenanceVehicleController;
use App\Http\Controllers\Maintenance\MaintenanceHistoryController;
use App\Http\Controllers\Maintenance\PrintMaintenanceController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/maintenance/vehicle-pdf/{vehicle}', ValueMaintenanceVehicleController::class)
        ->name('maintenance.vehicle-pdf');

    Route::get('/maintenance/history/{vehicle}', MaintenanceHistoryController::class)
        ->name('maintenance.history');

    Route::get('/maintenance/print', PrintMaintenanceController::class)
        ->name('maintenance.print');
});
```

---

## Fase 3: Bug Fix

### Job: CheckPreventiveMaintenance

**Archivo:** `app/Jobs/CheckPreventiveMaintenance.php`

**Problema:** Línea 72 envía notificaciones a todos los usuarios.

```php
// ❌ Actual (incorrecto)
Notification::make()
    ->sendToDatabase(User::all());

// ✅ corregir a:
Notification::make()
    ->sendToDatabase(User::role('coordinador')->get());
```

**Alternativa:** Crear notification channel dedicado para alertas de mantenimiento.

---

## Archivos a crear/modificar

| Acción | Archivos |
|--------|----------|
| **Crear** | 4 migraciones, 3 controladores, 3 vistas blade |
| **Modificar** | 4 modelos (añadir trait), 1 Job |
| **Instalar** | `barryvdh/laravel-dompdf` via Composer |

---

## Notas adicionales

1. **Verificar SparePart:** Revisar si necesita soft deletes basado en sus relaciones (`MaintenanceOrderPart`)
2. **Integración con Filament:** Las acciones de PDF pueden añadirse como botones en `MaintenanceOrderResource`
3. **Caché:** El documento menciona caché de 2 horas para PDFs - implementar con `Cache::remember()`
4. **Límite de imágenes:** Redimensionar a 800x600px si > 1MB (optimización documentada)

---

## Checklist de implementación

- [ ] Instalar `sarder/pdfstudio` y ejecutar `php artisan pdf-studio:install --all`
- [ ] Crear migración: `add_softDeletes_to_maintenance_types_table.php`
- [ ] Crear migración: `add_softDeletes_to_maintenance_orders_table.php`
- [ ] Crear migración: `add_softDeletes_to_maintenance_order_parts_table.php`
- [ ] Crear migración: `add_softDeletes_to_mechanics_table.php`
- [ ] Actualizar modelo: `MaintenanceType` con `SoftDeletes`
- [ ] Actualizar modelo: `MaintenanceOrder` con `SoftDeletes`
- [ ] Actualizar modelo: `MaintenanceOrderPart` con `SoftDeletes`
- [ ] Actualizar modelo: `Mechanic` con `SoftDeletes`
- [ ] Crear controlador: `ValueMaintenanceVehicleController`
- [ ] Crear controlador: `MaintenanceHistoryController`
- [ ] Crear controlador: `PrintMaintenanceController`
- [ ] Crear vista: `pdf/maintenance-vehicle.blade.php`
- [ ] Crear vista: `pdf/maintenance-history.blade.php`
- [ ] Crear vista: `pdf/maintenance-print.blade.php`
- [ ] Añadir rutas en `web.php`
- [ ] Arreglar Job: `CheckPreventiveMaintenance` línea 72
- [ ] Añadir acciones PDF en Filament Resource
- [ ] Ejecutar migraciones
- [ ] Correr tests