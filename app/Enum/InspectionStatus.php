<?php

declare(strict_types=1);

namespace App\Enum;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum InspectionStatus: string implements HasColor, HasLabel
{
    case GOOD = 'good';
    case WARNING = 'warning';
    case DANGER = 'danger';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::GOOD => 'Bueno',
            self::WARNING => 'Regular/Atención',
            self::DANGER => 'Crítico/Cambio',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::GOOD => 'success',
            self::WARNING => 'warning',
            self::DANGER => 'danger',
        };
    }
}
