<?php

namespace App\Enum;

enum typeContrat: string
{
    case CDI = 'CDI';
    case CDD = 'CDD';
    case INTERIM = 'Intérim';
    case STAGE = 'Stage';
    case ALTERNANCE = 'Alternance';
    case FREELANCE = 'Freelance';

    // Optionnel : méthode pour l'affichage dans le formulaire
    public function label(): string
    {
        return match($this) {
            self::CDI => 'CDI',
            self::CDD => 'CDD',
            self::INTERIM => 'Intérim',
            self::STAGE => 'Stage',
            self::ALTERNANCE => 'Alternance',
            self::FREELANCE => 'Freelance',
        };
    }
}

