<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Course\AddMember;

final readonly class Input
{
    public function __construct(public string $memberId, public string $courseId)
    {
    }
}
