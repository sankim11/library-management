<?php

namespace App\Service;


use App\Entity\Book;
use App\Entity\Loan;
use App\Entity\Member;
use App\Entity\Reservation;
use App\Enum\LoanStatus;
use App\Enum\ReservationStatus;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ReservationService
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function createReservation(Member $member, Book $book, \DateTimeInterface $date, ReservationStatus $status): Reservation
    {
        if ($book->getQuantity() <= 0) {
            throw new ValidationException('Book is out of stock.');
        }

        $reservation = new Reservation();
        $reservation->setMember($member);
        $reservation->setBook($book);
        $reservation->setReservationDate($date);
        $reservation->setStatus($status);

        $book->setQuantity($book->getQuantity() - 1);

        $this->entityManager->persist($reservation);
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $reservation;
    }


    public function cancelReservation(Reservation $reservation, Book $book): Reservation
    {
        $reservation->setStatus(ReservationStatus::CANCELED);

        $book->setQuantity($book->getQuantity() + 1);

        $this->entityManager->persist($reservation);
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $reservation;
    }

    public function confirmReservation(Reservation $reservation, Member $member, Book $book): Reservation
    {
        $reservation->setStatus(ReservationStatus::CONFIRMED);

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
        $this->entityManager->persist($reservation);
        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $reservation;
    }
}
