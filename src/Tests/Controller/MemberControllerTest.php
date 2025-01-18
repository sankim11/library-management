<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MemberControllerTest extends WebTestCase
{
    public function testCreateMember(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/members', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'role' => 'MEMBER',
        ]));

        $this->assertResponseStatusCodeSame(201);
    }
}
