<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\ORM\Repository;

use App\Domain;
use App\Infrastructure\Doctrine\ORM\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final readonly class Courses implements Domain\Data\Collection\Courses
{
    /**
     * @var ServiceEntityRepository<Course>
     */
    private ServiceEntityRepository $repository;

    public function __construct(private ManagerRegistry $registry)
    {
        $this->repository = new ServiceEntityRepository(
            $registry,
            Course::class
        );
    }

    public function add(Domain\Data\Model\Course $course, bool $flush = true): void
    {
        $this->registry->getManager()->persist($course);

        if ($flush) {
            $this->registry->getManager()->flush();
        }
    }

    public function find(string $id): Domain\Data\Model\Course
    {
        return $this->repository->find($id);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }
}
