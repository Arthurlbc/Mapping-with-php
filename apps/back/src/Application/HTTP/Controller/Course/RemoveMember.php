<?php

declare(strict_types=1);

namespace App\Application\HTTP\Controller\Course;

use App\Application\MessageBus;
use App\Domain;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class RemoveMember extends AbstractController
{
    public function __construct(
        private MessageBus $messageBus,
    ) {
    }

    #[Route('/course/{courseId}/members/{memberId}/remove', methods: ['GET', 'POST', 'DELETE'], name: 'remove_member')]
    public function __invoke(string $courseId, string $memberId): Response
    {
        try {
            $this->messageBus->transactional(new Domain\UseCase\Course\RemoveMember\Input(
                $memberId,
                $courseId
            ));
        } catch (Domain\Exception\MemberNotFound $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        $this->addFlash('success', 'Member has succefully removed');

        return $this->redirectToRoute(
            'course_details',
            ['id' => $courseId]
        );
    }
}
