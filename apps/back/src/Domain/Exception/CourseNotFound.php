<?php

declare(strict_types=1);

namespace App\Domain\Exception;

final class CourseNotFound extends \Exception
{
    public function __construct(private string $courseId)
    {
        parent::__construct("Course with id {$this->courseId} does not exist.");
    }
}
