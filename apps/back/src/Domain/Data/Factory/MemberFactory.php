<?php

declare(strict_types=1);

namespace App\Domain\Data\Factory;

use App\Domain\Data\Model\Member;

interface MemberFactory
{
    /**
     * @param array<string> $coursesComplete
     */
    public function create(
        string $name,
        array $coursesComplete,
    ): Member;
}
