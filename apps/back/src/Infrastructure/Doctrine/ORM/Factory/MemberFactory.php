<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\ORM\Factory;

use App\Domain;
use App\Infrastructure\Doctrine\ORM\Entity\Member;

final readonly class MemberFactory implements Domain\Data\Factory\MemberFactory
{
    public function create(string $name, array $coursesComplete): Member
    {
        return new Member($name, $coursesComplete);
    }
}
