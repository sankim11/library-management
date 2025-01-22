<?php

namespace App\Controller;

use App\ApiResource\DTO\BookInput;
use App\ApiResource\DTO\BookOutput;
use App\Entity\Book;
use App\Service\BookService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookController
{
    private ValidatorInterface $validator;
    private BookService $bookService;

    public function __construct(
        ValidatorInterface $validator,
        BookService $bookService
    ) {
        $this->validator = $validator;
        $this->bookService = $bookService;
    }

    #[Route('/api/get_books', name: 'get_books', methods: ['GET'])]
    public function getBooks(): JsonResponse
    {
        try {
            $books = $this->bookService->getBooks();
            return new JsonResponse($books, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/add_book', name: 'add_book', methods: ['POST'])]
    public function addBook(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $bookInput = new BookInput();
        $bookInput->title = $data['title'] ?? null;
        $bookInput->author = $data['author'] ?? null;
        $bookInput->isbn = $data['isbn'] ?? null;
        $bookInput->publishedDate = $data['published_date'] ?? null;
        $bookInput->quantity = $data['quantity'] ?? null;

        $errors = $this->validator->validate($bookInput);
        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        try {
            $book = $this->bookService->addBook($bookInput);
            $bookOutput = new BookOutput(
                $book->getId(),
                $book->getTitle(),
                $book->getAuthor(),
                $book->getIsbn(),
                $book->getPublishedDate(),
                $book->getQuantity()
            );

            return new JsonResponse($bookOutput, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/update_book/{id}', name: 'update_book', methods: ['PUT'])]
    public function updateBook(Request $request, Book $book): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $bookInput = new BookInput();
        $bookInput->title = $data['title'] ?? null;
        $bookInput->author = $data['author'] ?? null;
        $bookInput->isbn = $data['isbn'] ?? null;
        $bookInput->publishedDate = $data['published_date'] ?? null;
        $bookInput->quantity = $data['quantity'] ?? null;

        $errors = $this->validator->validate($bookInput);
        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        try {
            $updatedBook = $this->bookService->updateBook($book, $bookInput);
            $bookOutput = new BookOutput(
                $updatedBook->getId(),
                $updatedBook->getTitle(),
                $updatedBook->getAuthor(),
                $updatedBook->getIsbn(),
                $updatedBook->getPublishedDate(),
                $updatedBook->getQuantity()
            );

            return new JsonResponse($bookOutput, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/remove_book/{id}', name: 'remove_book', methods: ['DELETE'])]
    public function removeBook(Book $book): JsonResponse
    {
        try {
            $this->bookService->removeBook($book);
            return new JsonResponse(['message' => 'Book removed successfully!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/restore_book/{id}', name: 'restore_book', methods: ['POST'])]
    public function restoreBook(Book $book): JsonResponse
    {
        try {
            $restoredBook = $this->bookService->restoreBook($book);
            return new JsonResponse(['message' => 'Book restored successfully!'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
