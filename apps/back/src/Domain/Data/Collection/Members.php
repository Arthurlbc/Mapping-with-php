<?php

declare(strict_types=1);

namespace App\Domain\Data\Collection;

use App\Domain\Data\Model\Member;

interface Members
{
    public function add(Member $member): void;

    /**
     * @return array<Member>
     */
    public function findAll(): array;

    /**
     * @param array<string> $memberIds
     *
     * @return array<Member>
     */
    public function findMembers(array $memberIds): array;

    /**
     * @param array<string> $memberIds
     *
     * @return array<Member>
     */
    public function findNonMembers(array $memberIds): array;
}
