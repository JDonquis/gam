<?php

namespace App\Enums;

enum TypeActivityEnum: int
{
    // Usuarios
    case CREATE_USER = 1;
    case UPDATE_USER = 2;
    case DELETE_USER = 3;

        // Médicos
    case CREATE_DOCTOR = 4;
    case UPDATE_DOCTOR = 5;
    case DELETE_DOCTOR = 6;

        // Registros
    case INSERT_CENSUS = 7;
    case DELETE_CENSUS = 8;

        // Configuracion

    case INSERT_CONFIGURATION = 9;
    case UPDATE_CONFIGURATION = 10;
    case DELETE_CONFIGURATION = 11;

    case DELETE_INCIDENCE = 12;
    case DELETE_MULTIPLE_INCIDENCES = 13;
    case UPDATE_INCIDENCE = 14;

    case GENERATE_SANCTION = 15;
    case UPDATE_SANCTION = 16;
    case DELETE_SANCTION = 17;

    case GENERATE_RESIGNATION = 18;
    case UPDATE_RESIGNATION = 19;
    case DELETE_RESIGNATION = 20;





    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match ($this) {
            self::CREATE_USER => 'Crear usuario',
            self::UPDATE_USER => 'Actualizar usuario',
            self::DELETE_USER => 'Eliminar usuario',
            self::CREATE_DOCTOR => 'Crear médico',
            self::UPDATE_DOCTOR => 'Actualizar médico',
            self::DELETE_DOCTOR => 'Eliminar médico',
            self::INSERT_CENSUS => 'Insertar registro de censo',
            self::DELETE_CENSUS => 'Eliminar registro de censo',
            self::INSERT_CONFIGURATION => 'Crear configuracion de registro',
            self::UPDATE_CONFIGURATION => 'Actualizar configuracion de registro',
            self::DELETE_CONFIGURATION => 'Eliminar configuracion de registro',
            self::DELETE_INCIDENCE => 'Eliminar incidencia',
            self::DELETE_MULTIPLE_INCIDENCES => 'Eliminar multiples incidencias',
            self::UPDATE_INCIDENCE => 'Actualizar incidencia',
            self::GENERATE_SANCTION => 'Generar sancion',
            self::UPDATE_SANCTION => 'Editar sancion',
            self::DELETE_SANCTION => 'Eliminar sancion',

            self::GENERATE_RESIGNATION => 'Generar renuncia',
            self::UPDATE_RESIGNATION => 'Editar renuncia',
            self::DELETE_RESIGNATION => 'Eliminar renuncia',
        };
    }
}
