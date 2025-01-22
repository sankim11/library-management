<?php

namespace App\Controller;

use App\ApiResource\DTO\ReservationInput;
use App\ApiResource\DTO\ReservationOutput;
use App\Entity\Member;
use App\Entity\Book;
use App\Entity\Reservation;
use App\Enum\ReservationStatus;
use App\Service\ReservationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ReservationController
{
    private EntityManagerInterface $entityManager;
    private ReservationService $reservationService;
    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        ReservationService $reservationService,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->reservationService = $reservationService;
        $this->validator = $validator;
    }

    #[Route('/api/get_reservations_by_member/member', name: 'get_reservations_by_member', methods: ['GET'])]
    public function getReservationsByMember(Request $request): JsonResponse
    {
        $memberId = $request->query->get('member_id');

        if (!$memberId) {
            return new JsonResponse(['error' => 'Member ID is required'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $reservations = $this->reservationService->getReservationsByMember($memberId);
            return new JsonResponse($reservations, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/create_reservation', name: 'create_reservation', methods: ['POST'])]
    public function createReservation(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $reservationInput = new ReservationInput();
        $reservationInput->member = $data['member_id'] ?? null;
        $reservationInput->book = $data['book_id'] ?? null;
        $reservationInput->reservationDate = $data['reservation_date'] ?? null;
        $reservationInput->status = $data['status'] ?? 'PENDING';

        $errors = $this->validator->validate($reservationInput);
        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $member = $this->entityManager->getRepository(Member::class)->find($reservationInput->member);
        $book = $this->entityManager->getRepository(Book::class)->find($reservationInput->book);

        if (!$member || !$book) {
            return new JsonResponse(['error' => 'Invalid member or book.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $reservation = $this->reservationService->createReservation(
                $member,
                $book,
                new \DateTime($reservationInput->reservationDate),
                ReservationStatus::from($reservationInput->status)
            );

            $output = new ReservationOutput(
                $reservation->getId(),
                $reservation->getMember()->getId(),
                $reservation->getBook()->getId(),
                $reservation->getReservationDate(),
                $reservation->getStatus()->value
            );

            return new JsonResponse($output, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }


    #[Route('/api/cancel_reservation/', name: 'cancel_reservation', methods: ['PUT'])]
    public function cancelReservation(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['user_id'], $data['id'])) {
            return new JsonResponse(
                ['error' => 'Missing required fields: user_id or reservation_id'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $member = $this->entityManager->getRepository(Member::class)->find($data['user_id']);
        $reservation = $this->entityManager->getRepository(Reservation::class)->find($data['id']);

        if (!$reservation) {
            throw new \InvalidArgumentException("Reservation not found for ID: {$data['id']}");
        }

        $book = $reservation->getBook();

        if (!$member) {
            return new JsonResponse(['error' => 'Member not found.'], Response::HTTP_BAD_REQUEST);
        }

        if (!$book) {
            throw new \InvalidArgumentException("No book associated with this reservation.");
        }

        $reservation = $this->entityManager->getRepository(Reservation::class)->findOneBy([
            'member' => $member,
            'book' => $book,
            'status' => ReservationStatus::PENDING,
        ]);

        if (!$reservation) {
            return new JsonResponse(['error' => 'Reservation not found.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $canceledReservation = $this->reservationService->cancelReservation($reservation, $book);

            $output = new ReservationOutput(
                $canceledReservation->getId(),
                $canceledReservation->getMember()->getId(),
                $canceledReservation->getBook()->getId(),
                $canceledReservation->getReservationDate(),
                $canceledReservation->getStatus()->value
            );

            return new JsonResponse($output, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse('asfdas', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/api/confirm_reservation', name: 'confirm_reservation', methods: ['POST'])]
    public function confirmReservation(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['member_id'], $data['book_id'])) {
            return new JsonResponse(
                ['error' => 'Missing required fields: member_id or book_id'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $member = $this->entityManager->getRepository(Member::class)->find($data['member_id']);
        $book = $this->entityManager->getRepository(Book::class)->find($data['book_id']);

        if (!$member) {
            return new JsonResponse(['error' => 'Member not found.'], Response::HTTP_BAD_REQUEST);
        }

        if (!$book) {
            return new JsonResponse(['error' => 'Book not found.'], Response::HTTP_BAD_REQUEST);
        }

        $reservation = $this->entityManager->getRepository(Reservation::class)->findOneBy([
            'member' => $member,
            'book' => $book,
        ]);

        if (!$reservation) {
            return new JsonResponse(['error' => 'Reservation not found.'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $updatedReservation = $this->reservationService->confirmReservation($reservation, $member, $book);

            $output = new ReservationOutput(
                $updatedReservation->getId(),
                $updatedReservation->getMember()->getId(),
                $updatedReservation->getBook()->getId(),
                $updatedReservation->getReservationDate(),
                $updatedReservation->getStatus()->value
            );

            return new JsonResponse($output, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
