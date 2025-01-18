<?php

namespace App\Service;

use App\Entity\Loan;
use App\Entity\Member;
use App\Entity\Book;
use App\Enum\LoanStatus;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LoanService
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function loanBook(Member $member, Book $book): Loan
    {
        if ($book->getQuantity() <= 0) {
            throw new ValidationException('Book is out of stock for loan.');
        }

        $loan = new Loan();
        $loan->setMember($member);
        $loan->setBook($book);
        $loan->setLoanDate(new \DateTime());
        $loan->setStatus(LoanStatus::ACTIVE);

        $errors = $this->validator->validate($loan);
        if (count($errors) > 0) {
            throw new ValidationException((string) $errors);
        }

        $this->entityManager->persist($loan);
        $this->entityManager->flush();

        return $loan;
    }
}
