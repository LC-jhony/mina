<?php

namespace App\Enum;

use BackedEnum;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;

enum TripStatus: string implements HasColor, HasIcon, HasLabel
{
    case SCHEDULED = 'scheduled';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::SCHEDULED => 'Programado',
            self::IN_PROGRESS => 'En Progreso',
            self::COMPLETED => 'Completado',
            self::CANCELLED => 'Cancelado',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::SCHEDULED => 'info',
            self::IN_PROGRESS => 'warning',
            self::COMPLETED => 'success',
            self::CANCELLED => 'danger',
        };
    }

    public function getIcon(): string|BackedEnum|Htmlable|null
    {
        return match ($this) {
            self::SCHEDULED => Heroicon::Calendar,
            self::IN_PROGRESS => Heroicon::Clock,
            self::COMPLETED => Heroicon::Check,
            self::CANCELLED => Heroicon::XMark,
        };
    }
}
