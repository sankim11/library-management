<?php

namespace App\ApiResource\DTO;

class ReservationOutput
{
    public int $id;
    public int $member;
    public int $book;
    public \DateTimeInterface $reservationDate;

    public function __construct(
        int $id,
        int $member,
        int $book,
        \DateTimeInterface $reservationDate
    ) {
        $this->id = $id;
        $this->member = $member;
        $this->book = $book;
        $this->reservationDate = $reservationDate;
    }
}
