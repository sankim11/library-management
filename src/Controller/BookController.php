<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BookController
{
    private BookRepository $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        $this->bookRepository = $bookRepository;
    }

    #[Route('/book', name: 'add_book', methods: ['POST'])]
    public function addBook(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $title = $data['title'] ?? null;
        $author = $data['author'] ?? null;
        $isbn = $data['isbn'] ?? null;
        $publishedDate = $data['published_date'] ?? null;
        $quantity = $data['quantity'] ?? null;

        if (!$title || !$author || !$isbn || !$publishedDate || !$quantity) {
            return new JsonResponse(['error' => 'Missing required fields'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $book = new Book();
        $book->setTitle($title);
        $book->setAuthor($author);
        $book->setIsbn($isbn);
        $book->setPublishedDate(new \DateTime($publishedDate));
        $book->setQuantity((int)$quantity);

        try {
            $this->bookRepository->save($book);

            return new JsonResponse([
                'message' => 'Book added successfully!',
                'book_id' => $book->getId(),
            ], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
