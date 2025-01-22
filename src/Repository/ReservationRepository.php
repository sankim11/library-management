<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Enum\ReservationStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function getPendingReservationsByMember(int $memberId): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.member = :memberId')
            ->andWhere('r.status = :status')
            ->setParameter('memberId', $memberId)
            ->setParameter('status', ReservationStatus::PENDING)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
