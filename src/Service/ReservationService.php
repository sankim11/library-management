<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\Member;
use App\Entity\Book;
use App\Exception\ValidationException;
use App\Repository\BookRepository;
use App\Repository\MemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ReservationService
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private BookRepository $bookRepository;
    private MemberRepository $memberRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        BookRepository $bookRepository,
        MemberRepository $memberRepository
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->bookRepository = $bookRepository;
        $this->memberRepository = $memberRepository;
    }

    public function createReservation(string $memberId, string $bookId, \DateTimeInterface $date): Reservation
    {
        // Find Member and Book entities
        $member = $this->memberRepository->find($memberId);
        $book = $this->bookRepository->find($bookId);

        if (!$member) {
            throw new ValidationException('Member not found.');
        }

        if (!$book) {
            throw new ValidationException('Book not found.');
        }

        if ($book->getQuantity() <= 0) {
            throw new ValidationException('Book is out of stock for reservation.');
        }

        $reservation = new Reservation();
        $reservation->setMember($member);
        $reservation->setBook($book);
        $reservation->setReservationDate($date);

        $errors = $this->validator->validate($reservation);
        if (count($errors) > 0) {
            throw new ValidationException((string) $errors);
        }

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        return $reservation;
    }
}
