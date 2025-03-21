<?php

declare(strict_types=1);

namespace App\Application\HTTP\Controller;

use App\Domain\Data\Collection\Courses;
use App\Domain\Data\Collection\Members;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class Home extends AbstractController
{
    public function __construct(
        private Members $members,
        private Courses $courses,
    ) {
    }

    #[Route('/', methods: ['GET'])]
    public function __invoke(): Response
    {
        $courses = $this->courses->findAll();

        $members = [];
        foreach ($courses as $course) {
            $memberIds = $course->getMemberIds();

            if (!empty($memberIds)) {
                $courseMembers = $this->members->findMembers($memberIds);
            } else {
                $courseMembers = [];
            }

            $members[$course->getId()] = $courseMembers;
        }

        return $this->render('courses/index.html.twig', [
            'courses' => $courses,
            'members' => $members,
        ]);
    }
}
