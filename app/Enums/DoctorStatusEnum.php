<?php

namespace App\Enums;

enum DoctorStatusEnum: int
{
    case AVAILABLE = 1;
    case IN_COURSE = 2;
    case WITH_INCIDENCE = 3;
    case SANCTIONED = 4;


    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function allWithLabels(): array
{
    return array_map(function ($case) {
        return [
            'value' => $case->value,
            'label' => $case->label()
        ];
    }, self::cases());
}

    public function label(): string
    {
        return match($this) {
            self::AVAILABLE => 'Disponible',
            self::IN_COURSE => 'En curso',
            self::WITH_INCIDENCE => 'Con incidencia',
            self::SANCTIONED => 'Sancionado',
        };
    }
}
