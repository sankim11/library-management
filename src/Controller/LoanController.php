<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Member;
use App\Entity\Loan;
use App\Enum\LoanStatus;
use App\Service\LoanService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LoanController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private LoanService $loanService
    ) {}

    #[Route('/api/create_loan', name: 'create_loan', methods: ['POST'])]
    public function createLoan(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['member_id'], $data['book_id'], $data['loan_date'], $data['status'])) {
            return new JsonResponse(
                ['error' => 'Missing required fields: member_id, book_id, loan_date, or status'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $member = $this->entityManager->getRepository(Member::class)->find($data['member_id']);
        $book = $this->entityManager->getRepository(Book::class)->find($data['book_id']);
        $loanDate = new \DateTime($data['loan_date']);
        $returnDate = $data['return_date'] !== null ? new \DateTime($data['return_date']) : null;

        if (!$member) {
            return new JsonResponse(['error' => 'Invalid member_id'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!$book) {
            return new JsonResponse(['error' => 'Invalid book_id'], JsonResponse::HTTP_BAD_REQUEST);
        }

        // Check if user already has an active loan for book
        $existingLoan = $this->entityManager->getRepository(Loan::class)->findOneBy([
            'member' => $member,
            'book' => $book,
            'status' => 'ACTIVE',
        ]);

        if ($existingLoan) {
            return new JsonResponse(
                ['error' => 'You already have this book loaned. Please return it first.'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        try {
            $status = LoanStatus::from($data['status']);
        } catch (\ValueError $e) {
            return new JsonResponse(
                ['error' => 'Invalid status value. Allowed values are: ACTIVE, OVERDUE, and COMPLETED'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        try {
            $loan = $this->loanService->createLoan($member, $book, $loanDate, $returnDate, $status);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }

        return new JsonResponse([
            'message' => 'Loan created successfully!',
            'loan_id' => $loan->getId(),
        ], JsonResponse::HTTP_CREATED);
    }

    #[Route('/api/return_loan', name: 'return_loan', methods: ['POST'])]
    public function returnLoan(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['member_id'], $data['book_id'])) {
            return new JsonResponse(
                ['error' => 'Missing required field: loan_id'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        $member = $this->entityManager->getRepository(Member::class)->find($data['member_id']);
        $book = $this->entityManager->getRepository(Book::class)->find($data['book_id']);

        if (!$member) {
            return new JsonResponse(['error' => 'Invalid member_id'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!$book) {
            return new JsonResponse(['error' => 'Invalid book_id'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $loan = $this->entityManager->getRepository(Loan::class)->findOneBy([
            'member' => $member,
            'book' => $book,
            'status' => 'ACTIVE', // Ensure the book is currently loaned
        ]);

        if (!$loan) {
            return new JsonResponse(
                ['error' => 'No active loan found for the given member and book'],
                JsonResponse::HTTP_BAD_REQUEST
            );
        }

        try {
            $returnedLoan = $this->loanService->returnLoan($loan);

            return new JsonResponse([
                'message' => 'Book returned successfully!',
                'loan_id' => $returnedLoan->getId(),
                'return_date' => $returnedLoan->getReturnDate()->format('Y-m-d H:i:s'),
                'status' => $returnedLoan->getStatus(),
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
