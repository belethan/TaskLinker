<?php
namespace App\Enum;

enum statutTache: string
{
    case TODO = 'TO DO';
    case DOING = 'DOING';
    case DONE = 'DONE';

    public function label(): string
    {
        return match ($this) {
            self::TODO => 'To Do',
            self::DOING => 'Doing',
            self::DONE => 'Done',
        };
    }
}
