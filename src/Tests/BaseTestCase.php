<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseTestCase extends WebTestCase
{
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    protected function request(string $method, string $url, array $data = []): Response
    {
        $this->client->request($method, $url, [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));
        return $this->client->getResponse();
    }

    protected function createTestData(): void
    {
        // Use the EntityManager to persist test data
        $entityManager = self::getContainer()->get(EntityManagerInterface::class);

        // Create a test member
        $member = new \App\Entity\Member();
        $member->setName('Test Member');
        $member->setEmail('test@example.com');
        $member->setPassword('password123'); // Assume a hashed password
        $member->setRole(\App\Enum\Role::MEMBER);
        $entityManager->persist($member);

        // Create a test book
        $book = new \App\Entity\Book();
        $book->setTitle('Test Book');
        $book->setAuthor('Test Author');
        $book->setIsbn('123456789');
        $book->setPublishedDate(new \DateTime('2023-01-01'));
        $book->setQuantity(10);
        $entityManager->persist($book);

        // Save to the database
        $entityManager->flush();
    }
}
