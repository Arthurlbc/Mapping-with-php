<?php

declare(strict_types=1);

namespace App\Application\HTTP\Controller\Course;

use App\Domain;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class CourseDetails extends AbstractController
{
    public function __construct(
        private Domain\Data\Collection\Courses $courses,
        private Domain\Data\Collection\Members $members,
    ) {
    }

    #[Route('/course/{id}', methods: ['GET'], name: 'course_details')]
    public function __invoke(string $id): Response
    {
        $course = $this->courses->find($id);

        if (null == $course) {
            $this->addFlash('error', 'Course not found.');

            return new JsonResponse(null);
        }
        if ($course->getMemberIds()) {
            $members = $this->members->findMembers($course->getMemberIds());
            $availableMembers = $this->members->findNonMembers($course->getMemberIds());
        } else {
            $members = null;
            $availableMembers = $this->members->findAll();
        }

        return $this->render('courses/details.html.twig', [
            'course' => $course,
            'members' => $members,
            'availableMembers' => $availableMembers,
        ]);
    }
}
