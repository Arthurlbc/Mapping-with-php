<?php

declare(strict_types=1);

namespace App\Domain\Data\Enum;

enum Currency: string
{
    case EUR = '€';
    case DOLLAR = '$';

    public function value(): string
    {
        return $this->value;
    }
}
