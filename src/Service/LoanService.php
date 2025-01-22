<?php

namespace App\Service;

use App\Entity\Loan;
use App\Entity\Book;
use App\Entity\Member;
use App\Enum\LoanStatus;
use App\Repository\LoanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LoanService
{
    private LoanRepository $loanRepository;
    private ValidatorInterface $validator;
    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        LoanRepository $loanRepository
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->loanRepository = $loanRepository;
    }

    public function createLoan(Member $member, Book $book, \DateTimeInterface $loanDate, ?\DateTimeInterface $returnDate, LoanStatus $status): Loan
    {
        if ($book->getQuantity() <= 0) {
            throw new \Exception('Book is out of stock.');
        }

        $loan = new Loan();
        $loan->setMember($member);
        $loan->setBook($book);
        $loan->setLoanDate($loanDate);
        $loan->setReturnDate($returnDate);
        $loan->setStatus($status);

        $errors = $this->validator->validate($loan);
        if (count($errors) > 0) {
            throw new \Exception((string) $errors);
        }

        $book->setQuantity($book->getQuantity() - 1);

        $this->entityManager->persist($loan);
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $loan;
    }


    public function returnLoan(Loan $loan): Loan
    {
        if ($loan->getStatus() === 'RETURNED') {
            throw new \Exception('The loan has already been returned.');
        }

        $loan->setReturnDate(new \DateTime());
        $loan->setStatus(LoanStatus::RETURNED);

        $book = $loan->getBook();
        $book->setQuantity($book->getQuantity() + 1);

        $this->entityManager->persist($loan);
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $loan;
    }

    public function getActiveLoans(): array
    {
        return $this->loanRepository->findActiveLoans();
    }

    public function getUpcomingLoansByMember(int $memberId): array
    {
        return $this->loanRepository->findUpcomingLoansByMember($memberId);
    }

    public function getLoansByMember(int $memberId): array
    {
        return $this->loanRepository->findLoansByMember($memberId);
    }

    public function extendLoanReturnDate(int $loanId, \DateTimeInterface $newReturnDate): Loan
    {
        $loan = $this->loanRepository->find($loanId);

        if (!$loan) {
            throw new \Exception("Loan not found.");
        }

        if ($loan->getStatus() !== LoanStatus::ACTIVE) {
            throw new \Exception("Only active loans can extend return date.");
        }

        if ($loan->getReturnDate() && $loan->getReturnDate() > $newReturnDate) {
            throw new \Exception("New return date must be after the current return date.");
        }

        $loan->setReturnDate($newReturnDate);
        $this->entityManager->persist($loan);
        $this->entityManager->flush();

        return $loan;
    }
}
