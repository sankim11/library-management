<?php

namespace App\Service;

use App\Entity\Loan;
use App\Entity\Book;
use App\Entity\Member;
use App\Enum\LoanStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LoanService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {}

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
}
