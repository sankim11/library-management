<?php

namespace App\Tests\Entity;

use App\Entity\Reservation;
use App\Entity\Member;
use App\Entity\Book;
use PHPUnit\Framework\TestCase;

class ReservationTest extends TestCase
{
    public function testReservationEntity(): void
    {
        $reservation = new Reservation();
        $member = new Member();
        $book = new Book();
        $reservationDate = new \DateTime();

        $reservation->setMember($member)
            ->setBook($book)
            ->setReservationDate($reservationDate);

        $this->assertSame($member, $reservation->getMember());
        $this->assertSame($book, $reservation->getBook());
        $this->assertSame($reservationDate, $reservation->getReservationDate());
    }
}
