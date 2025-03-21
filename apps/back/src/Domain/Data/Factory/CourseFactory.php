<?php

declare(strict_types=1);

namespace App\Domain\Data\Factory;

use App\Domain\Data\Model\Course;
use App\Domain\Data\ValueObject\Author;

interface CourseFactory
{
    /**
     * @param array<string> $memberIds
     */
    public function create(
        string $name,
        string $description,
        int $duration,
        Author $author,
        array $memberIds,
    ): Course;
}
