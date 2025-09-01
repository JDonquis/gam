<?php

namespace App\Enums;

enum TypeDocumentEnum: int
{
    case CENSO_RESIDENTES = 1;
    case RENUNCUAS = 2;


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
        return match ($this) {
            self::CENSO_RESIDENTES => 'Censo de residentes',
            self::RENUNCUAS => 'Renuncias',
        };
    }
}
