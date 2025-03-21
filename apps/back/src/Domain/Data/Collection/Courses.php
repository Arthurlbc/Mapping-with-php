<?php

declare(strict_types=1);

namespace App\Domain\Data\Collection;

use App\Domain\Data\Model\Course;

interface Courses
{
    public function add(Course $course): void;

    public function find(string $id): ?Course;

    /**
     * @return array<Course>
     */
    public function findAll(): array;
}
