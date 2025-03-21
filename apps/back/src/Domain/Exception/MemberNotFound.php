<?php

declare(strict_types=1);

namespace App\Domain\Exception;

final class MemberNotFound extends \Exception
{
    public function __construct(private string $memberId, private string $courseName)
    {
        parent::__construct("This member {$memberId} is not found in this course [$courseName}");
    }
}
