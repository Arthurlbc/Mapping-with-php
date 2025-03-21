<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\ORM\Repository;

use App\Domain;
use App\Infrastructure\Doctrine\ORM\Entity\Member;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final readonly class Members implements Domain\Data\Collection\Members
{
    /**
     * @var ServiceEntityRepository<Member>
     */
    private ServiceEntityRepository $repository;

    public function __construct(private ManagerRegistry $registry)
    {
        $this->repository = new ServiceEntityRepository(
            $registry,
            Member::class
        );
    }

    public function add(Domain\Data\Model\Member $member): void
    {
        $this->registry->getManager()->persist($member);
        $this->registry->getManager()->flush();
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function findMembers(array $ids): array
    {
        /**
         * @var array<Domain\Data\Model\Member>
         */
        return $this->repository->createQueryBuilder('members')
            ->where('members.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }

    public function findNonMembers(array $ids): array
    {
        /**
         * @var array<Domain\Data\Model\Member>
         */
        return $this->repository->createQueryBuilder('members')
            ->where('members.id NOT IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }
}
