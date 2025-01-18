<?php

namespace App\Tests\Service;

use App\Entity\Reservation;
use App\Service\ReservationService;
use PHPUnit\Framework\TestCase;

class ReservationServiceTest extends TestCase
{
    public function testCreateReservation(): void
    {
        $reservationService = $this->createMock(ReservationService::class);

        $reservation = new Reservation();
        $reservationService->expects($this->once())
            ->method('createReservation')
            ->willReturn($reservation);

        $this->assertInstanceOf(Reservation::class, $reservation);
    }
}
