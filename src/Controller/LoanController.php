<?php

namespace App\Controller;

use App\ApiResource\DTO\LoanInput;
use App\ApiResource\DTO\LoanOutput;
use App\Entity\Book;
use App\Entity\Member;
use App\Entity\Loan;
use App\Enum\LoanStatus;
use App\Service\LoanService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LoanController
{
    private EntityManagerInterface $entityManager;
    private LoanService $loanService;
    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoanService $loanService,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->loanService = $loanService;
        $this->validator = $validator;
    }

    #[Route('/api/create_loan', name: 'create_loan', methods: ['POST'])]
    public function createLoan(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse(['error' => 'Invalid JSON payload'], Response::HTTP_BAD_REQUEST);
        }

        $loanInput = new LoanInput();
        $loanInput->member_id = $data['member_id'] ?? null;
        $loanInput->book_id = $data['book_id'] ?? null;
        $loanInput->loan_date = $data['loan_date'] ?? null;
        $loanInput->return_date = $data['return_date'] ?? null;
        $loanInput->status = $data['status'] ?? null;

        $errors = $this->validator->validate($loanInput);
        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $member = $this->entityManager->getRepository(Member::class)->find($loanInput->member_id);
        $book = $this->entityManager->getRepository(Book::class)->find($loanInput->book_id);

        if (!$member) {
            return new JsonResponse(['error' => 'Invalid member_id'], Response::HTTP_BAD_REQUEST);
        }

        if (!$book) {
            return new JsonResponse(['error' => 'Invalid book_id'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $loan = $this->loanService->createLoan(
                $member,
                $book,
                new \DateTime($loanInput->loan_date),
                $loanInput->return_date ? new \DateTime($loanInput->return_date) : null,
                LoanStatus::from($loanInput->status)
            );

            $output = new LoanOutput(
                $loan->getId(),
                $loan->getMember()->getName(),
                $loan->getBook()->getTitle(),
                $loan->getLoanDate()->format('Y-m-d'),
                $loan->getReturnDate()?->format('Y-m-d'),
                $loan->getStatus()->value
            );

            return new JsonResponse($output, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/return_loan', name: 'return_loan', methods: ['POST'])]
    public function returnLoan(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['member_id'], $data['book_id'])) {
            return new JsonResponse(['error' => 'Missing required fields: member_id or book_id'], Response::HTTP_BAD_REQUEST);
        }

        $member = $this->entityManager->getRepository(Member::class)->find($data['member_id']);
        $book = $this->entityManager->getRepository(Book::class)->find($data['book_id']);

        if (!$member) {
            return new JsonResponse(['error' => 'Invalid member_id'], Response::HTTP_BAD_REQUEST);
        }

        if (!$book) {
            return new JsonResponse(['error' => 'Invalid book_id'], Response::HTTP_BAD_REQUEST);
        }

        $loan = $this->entityManager->getRepository(Loan::class)->findOneBy([
            'member' => $member,
            'book' => $book,
            'status' => LoanStatus::ACTIVE,
        ]);

        if (!$loan) {
            return new JsonResponse(['error' => 'No active loan found for the given member and book'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $returnedLoan = $this->loanService->returnLoan($loan);

            $output = new LoanOutput(
                $returnedLoan->getId(),
                $returnedLoan->getMember()->getName(),
                $returnedLoan->getBook()->getTitle(),
                $returnedLoan->getLoanDate()->format('Y-m-d'),
                $returnedLoan->getReturnDate()?->format('Y-m-d'),
                $returnedLoan->getStatus()->value
            );

            return new JsonResponse($output, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/loans/active', name: 'get_active_loans', methods: ['GET'])]
    public function getActiveLoans(): JsonResponse
    {
        try {
            $activeLoans = $this->loanService->getActiveLoans();
            return new JsonResponse($activeLoans, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/get_loans_by_member', name: 'get_loans_by_member', methods: ['GET'])]
    public function getLoansByMember(Request $request): JsonResponse
    {
        $memberId = $request->query->get('member_id');

        if (!$memberId) {
            return new JsonResponse(['error' => 'Member ID is required'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $loans = $this->loanService->getLoansByMember($memberId);
            return new JsonResponse($loans, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/get_upcoming_loans_by_member', name: 'get_upcoming_loans_by_member    ', methods: ['GET'])]
    public function getUpcomingLoansByMember(Request $request): JsonResponse
    {
        $memberId = $request->query->get('member_id');

        if (!$memberId) {
            return new JsonResponse(['error' => 'Member ID is required'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $upcomingLoans = $this->loanService->getUpcomingLoansByMember($memberId);
            return new JsonResponse($upcomingLoans, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/loan/extend', name: 'extend_loan', methods: ['POST'])]
    public function extendLoan(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['loan_id'], $data['new_return_date'])) {
            return new JsonResponse(['error' => 'Missing required fields: loan_id or new_return_date'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $newReturnDate = new \DateTime($data['new_return_date']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid date format for new_return_date'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $loan = $this->loanService->extendLoanReturnDate($data['loan_id'], $newReturnDate);

            $loanOutput = new LoanOutput(
                $loan->getId(),
                $loan->getMember()->getName(),
                $loan->getBook()->getTitle(),
                $loan->getLoanDate()->format('Y-m-d'),
                $loan->getReturnDate()->format('Y-m-d'),
                $loan->getStatus()->value
            );

            return new JsonResponse($loanOutput, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
