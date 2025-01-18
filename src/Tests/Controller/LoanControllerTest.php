<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoanControllerTest extends WebTestCase
{
    public function testLoanBook(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/loans', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'memberId' => 1,
            'bookId' => 1,
        ]));

        $this->assertResponseStatusCodeSame(201);
    }
}
