<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\ORM\Factory;

use App\Domain;
use App\Infrastructure\Doctrine\ORM\Entity\Course;

final readonly class CourseFactory implements Domain\Data\Factory\CourseFactory
{
    public function create(
        string $name,
        string $description,
        int $duration,
        Domain\Data\ValueObject\Author $author,
        array $memberIds,
    ): Course {
        return new Course($name, $description, $duration, $author, $memberIds);
    }
}
