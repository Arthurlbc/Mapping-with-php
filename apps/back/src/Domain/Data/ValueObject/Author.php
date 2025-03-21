<?php

declare(strict_types=1);

namespace App\Domain\Data\ValueObject;

final class Author implements \Stringable
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $organization,
    ) {
    }

    public function __toString(): string
    {
        return "{$this->firstName} {$this->lastName} from {$this->organization}";
    }
}
