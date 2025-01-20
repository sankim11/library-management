<?php

namespace App\ApiResource\DTO;

use App\Enum\ReservationStatus;
use Symfony\Component\Validator\Constraints as Assert;

class ReservationInput
{
    #[Assert\NotBlank(message: "Member is required.")]
    #[Assert\Type(type: 'string', message: "Member must be a string.")]
    public ?string $member = null;

    #[Assert\NotBlank(message: "Book is required.")]
    #[Assert\Type(type: 'string', message: "Book must be a string.")]
    public ?string $book = null;

    #[Assert\NotBlank(message: "Reservation date is required.")]
    #[Assert\Date(message: "Reservation date must be a valid date.")]
    public ?string $reservationDate = null;

    #[Assert\NotBlank(message: "Reservation status is required.")]
    #[Assert\Choice(
        choices: ["PENDING", "CONFIRMED", "CANCELED"],
        message: "Invalid status value. Allowed values are: PENDING, CONFIRMED, CANCELED."
    )]
    public ?string $status = null;
}
