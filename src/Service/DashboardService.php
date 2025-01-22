<?php

namespace App\Service;

use App\Entity\Loan;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;

class DashboardService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getDashboardStats(int $memberId): array
    {
        $activeLoans = $this->entityManager->getRepository(Loan::class)->getActiveLoansByMember($memberId);
        $pendingReservations = $this->entityManager->getRepository(Reservation::class)->getPendingReservationsByMember($memberId);
        $booksRead = $this->entityManager->getRepository(Loan::class)->getBooksReadByMember($memberId);
        $overdueLoans = $this->entityManager->getRepository(Loan::class)->getOverdueLoansByMember($memberId);

        return [
            'activeLoans' => $activeLoans,
            'pendingReservations' => $pendingReservations,
            'booksRead' => $booksRead,
            'overdue' => $overdueLoans,
        ];
    }
}
