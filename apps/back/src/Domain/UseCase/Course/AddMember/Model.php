<?php

declare(strict_types=1);

namespace App\Domain\UseCase\Course\AddMember;

use App\Domain\Data\Collection\Courses;
use App\Domain\Data\Collection\Members;
use App\Domain\Exception\MemberNotFound;

final readonly class Model
{
    public function __construct(
        private Courses $courses,
        private Members $members,
    ) {}

    public function __invoke(Input $input): Output
    {

        $course = $this->courses->find($input->courseId);

        if ($course->hasMember($input->memberId)) {
            throw new MemberNotFound($input->memberId, $course->getName());
        }

        $course->addMember($input->memberId);

        return new Output();
    }
}
