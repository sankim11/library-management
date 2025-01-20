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

        // Map input data to BookInput DTO
        $bookInput = new BookInput();
        $bookInput->title = $data['title'] ?? null;
        $bookInput->author = $data['author'] ?? null;
        $bookInput->isbn = $data['isbn'] ?? null;
        $bookInput->publishedDate = $data['published_date'] ?? null;
        $bookInput->quantity = $data['quantity'] ?? null;

        // Validate the DTO
        $errors = $this->validator->validate($bookInput);
        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string) $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Convert BookInput to Book entity
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

        // Persist the Book entity
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
}
