<?php

namespace App\Repository;

use App\Entity\Loan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Loan>
 */
class LoanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Loan::class);
    }

    public function findActiveLoans(): array
    {
        return $this->createQueryBuilder('l')
            ->select('l.id, l.loanDate, l.returnDate, l.status, b.title as bookTitle, m.name as memberName')
            ->join('l.book', 'b')
            ->join('l.member', 'm')
            ->where('l.status = :status')
            ->setParameter('status', 'ACTIVE')
            ->orderBy('l.loanDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findLoansByMember(int $memberId): array
    {
        return $this->createQueryBuilder('l')
            ->select('l.id, l.loanDate, l.returnDate, l.status, b.title as bookTitle')
            ->join('l.book', 'b')
            ->where('l.member = :memberId')
            ->setParameter('memberId', $memberId)
            ->orderBy('l.loanDate', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getActiveLoansByMember(int $memberId): int
    {
        return $this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->where('l.member = :memberId')
            ->andWhere('l.status = :status')
            ->setParameter('memberId', $memberId)
            ->setParameter('status', 'ACTIVE')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findUpcomingLoansByMember(int $memberId): array
    {
        return $this->createQueryBuilder('l')
            ->select('l.id, l.loanDate, l.returnDate, l.status, b.title as bookTitle')
            ->join('l.book', 'b')
            ->where('l.member = :memberId')
            ->andWhere('l.returnDate > :today')
            ->setParameter('memberId', $memberId)
            ->setParameter('today', new \DateTime())
            ->getQuery()
            ->getResult();
    }

    public function getBooksReadByMember(int $memberId): int
    {
        return $this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->where('l.member = :memberId')
            ->andWhere('l.status = :status')
            ->setParameter('memberId', $memberId)
            ->setParameter('status', 'RETURNED')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getOverdueLoansByMember(int $memberId): int
    {
        return $this->createQueryBuilder('l')
            ->select('COUNT(l.id)')
            ->where('l.member = :memberId')
            ->andWhere('l.returnDate < :today')
            ->andWhere('l.status = :status')
            ->setParameter('memberId', $memberId)
            ->setParameter('today', new \DateTime())
            ->setParameter('status', 'ACTIVE')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
