<?php

namespace App\Controller;

use App\Entity\Book;
use App\ApiResource\DTO\BookInput;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BookController
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    #[Route('/api/add_book', name: 'add_book', methods: ['POST'])]
    public function addBook(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON payload'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $bookInput = new BookInput();
        $bookInput->title = $data['title'] ?? null;
        $bookInput->author = $data['author'] ?? null;
        $bookInput->isbn = $data['isbn'] ?? null;
        $bookInput->publishedDate = $data['published_date'] ?? null;
        $bookInput->quantity = $data['quantity'] ?? null;

        $errors = $this->validator->validate($bookInput);
        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string) $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        $book = new Book();
        $book->setTitle($bookInput->title);
        $book->setAuthor($bookInput->author);
        $book->setIsbn($bookInput->isbn);

        try {
            $book->setPublishedDate(new \DateTime($bookInput->publishedDate));
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid published_date format. Use YYYY-MM-DD.'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $book->setQuantity($bookInput->quantity);

        try {
            $this->entityManager->persist($book);
            $this->entityManager->flush();

            return new JsonResponse(
                [
                    'message' => 'Book added successfully!',
                    'book_id' => $book->getId(),
                ],
                JsonResponse::HTTP_CREATED
            );
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/remove_book', name: 'remove_book', methods: ['DELETE'])]
    public function removeBook(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['book_id'])) {
            return new JsonResponse(
                ['error' => 'Missing required field: book_id'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $book = $this->entityManager->getRepository(Book::class)->find($data['book_id']);

        if (!$book) {
            return new JsonResponse(['error' => 'Book not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        if ($book->isDeleted()) {
            return new JsonResponse(['error' => 'Book is already removed'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $book->setDeletedAt(new \DateTime());

        try {
            $this->entityManager->flush();
            return new JsonResponse(['message' => 'Book removed successfully'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => 'An error occurred while removing the book: ' . $e->getMessage()],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/api/restore_book', name: 'restore_book', methods: ['POST'])]
    public function restoreBook(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['book_id'])) {
            return new JsonResponse(
                ['error' => 'Missing required field: book_id'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $book = $this->entityManager->getRepository(Book::class)->find($data['book_id']);

        if (!$book) {
            return new JsonResponse(['error' => 'Book not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        if (!$book->isDeleted()) {
            return new JsonResponse(['error' => 'Book is not removed'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $book->setDeletedAt(null);

        try {
            $this->entityManager->flush();
            return new JsonResponse(['message' => 'Book restored successfully'], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(
                ['error' => 'An error occurred while restoring the book: ' . $e->getMessage()],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
