<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Member;
use App\Service\LoanService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LoanController
{
    private LoanService $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    #[Route('/loan', name: 'loan_book', methods: ['POST'])]
    public function loanBook(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Extract Member and Book from the request
        $memberId = $data['member_id'] ?? null;
        $bookId = $data['book_id'] ?? null;

        if (!$memberId || !$bookId) {
            return new JsonResponse(['error' => 'Missing member_id or book_id'], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $loan = $this->loanService->loanBook($memberId, $bookId);
            return new JsonResponse([
                'message' => 'Loan created successfully!',
                'loan_id' => $loan->getId(),
            ], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
