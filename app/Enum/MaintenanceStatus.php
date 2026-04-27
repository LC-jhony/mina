<?php

namespace App\Enum;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

enum MaintenanceStatus: string implements HasColor, HasIcon, HasLabel
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Pendiente',
            self::IN_PROGRESS => 'En Progreso',
            self::COMPLETED => 'Completado',
            self::CANCELLED => 'Cancelado',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::IN_PROGRESS => 'warning',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
        };
    }

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::PENDING => Heroicon::Clock,
            self::IN_PROGRESS => Heroicon::Wrench,
            self::COMPLETED => Heroicon::CheckCircle,
            self::CANCELLED => Heroicon::XCircle,
        };
    }
}
