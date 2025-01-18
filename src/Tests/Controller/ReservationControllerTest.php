<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReservationControllerTest extends WebTestCase
{
    public function testCreateReservation(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/reservations', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'memberId' => 1,
            'bookId' => 1,
            'reservationDate' => '2025-01-17',
        ]));

        $this->assertResponseStatusCodeSame(201);
    }
}
