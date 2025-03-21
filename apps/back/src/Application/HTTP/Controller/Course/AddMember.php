<?php

declare(strict_types=1);

namespace App\Application\HTTP\Controller\Course;

use App\Application\MessageBus;
use App\Domain;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AddMember extends AbstractController
{
    public function __construct(
        private MessageBus $messageBus,
    ) {
    }

    #[Route('/course/{courseId}/members/add', methods: ['POST'], name: 'add_member')]
    public function __invoke(Request $request, string $courseId): Response
    {
        $memberId = $request->request->getString('memberId');

        try {
            $this->messageBus->transactional(new Domain\UseCase\Course\AddMember\Input(
                $memberId,
                $courseId
            ));
        } catch (Domain\Exception\MemberNotFound $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        $this->addFlash('success', 'Member has succefully added');

        return $this->redirectToRoute(
            'course_details',
            ['id' => $courseId]
        );
    }
}
