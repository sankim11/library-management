<?php

namespace App\Tests\Entity;

use App\Entity\Book;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
    public function testBookEntity(): void
    {
        $book = new Book();
        $book->setTitle('Test Book')
            ->setAuthor('Author')
            ->setIsbn('123-456-789')
            ->setQuantity(10);

        $this->assertEquals('Test Book', $book->getTitle());
        $this->assertEquals('Author', $book->getAuthor());
        $this->assertEquals('123-456-789', $book->getIsbn());
        $this->assertEquals(10, $book->getQuantity());
    }
}
