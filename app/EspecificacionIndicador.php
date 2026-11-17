<?php

namespace App;

enum EspecificacionIndicador: string
{
    case Carrera = 'carrera';
    case Facultad = 'facultad';
    case Departamento = 'departamento';
    case Grupo = 'grupo';
    case NivelAcademico = 'nivel_academico';

    public function label(): string
    {
        return match ($this) {
            self::Carrera => 'Carrera',
            self::Facultad => 'Facultad',
            self::Departamento => 'Departamento',
            self::Grupo => 'Grupo',
            self::NivelAcademico => 'Nivel académico',
        };
    }
}
