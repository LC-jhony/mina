<?php

namespace App\Enum;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

enum VehicleStatus: string implements HasColor, HasIcon, HasLabel
{
    case Available = 'available';
    case OnTrip = 'on_trip';
    case InMaintenance = 'in_maintenance';
    case OutOfService = 'out_of_service';

    public function getLabel(): string
    {
        return match ($this) {
            self::Available => 'Disponible',
            self::OnTrip => 'En viaje',
            self::InMaintenance => 'En mantenimiento',
            self::OutOfService => 'Fuera de servicio',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Available => 'success',
            self::OnTrip => 'warning',
            self::InMaintenance => 'danger',
            self::OutOfService => 'secondary',
        };
    }

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::Available => Heroicon::Check,
            self::OnTrip => Heroicon::Truck,
            self::InMaintenance => Heroicon::Wrench,
            self::OutOfService => Heroicon::XMark,
        };
    }
}
