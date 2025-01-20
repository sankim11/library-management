<?php

namespace App\Service;

use App\ApiResource\DTO\BookInput;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;

class BookService
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function addBook(BookInput $bookInput): Book
    {
        $book = new Book();
        $book->setTitle($bookInput->title);
        $book->setAuthor($bookInput->author);
        $book->setIsbn($bookInput->isbn);
        $book->setPublishedDate(new \DateTime($bookInput->publishedDate));
        $book->setQuantity($bookInput->quantity);

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $book;
    }

    public function updateBook(Book $book, BookInput $bookInput): Book
    {
        if ($bookInput->title) {
            $book->setTitle($bookInput->title);
        }
        if ($bookInput->author) {
            $book->setAuthor($bookInput->author);
        }
        if ($bookInput->isbn) {
            $book->setIsbn($bookInput->isbn);
        }
        if ($bookInput->publishedDate) {
            $book->setPublishedDate(new \DateTime($bookInput->publishedDate));
        }
        if ($bookInput->quantity) {
            $book->setQuantity($bookInput->quantity);
        }

        $this->entityManager->flush();

        return $book;
    }

    public function removeBook(Book $book): Book
    {
        $book->setDeletedAt(new \DateTime());
        $this->entityManager->flush();

        return $book;
    }

    public function restoreBook(Book $book): Book
    {
        $book->setDeletedAt(null);
        $this->entityManager->flush();

        return $book;
    }
}
