<?php

namespace App\Controller;

use App\ApiResource\DTO\ReservationInput;
use App\ApiResource\DTO\ReservationOutput;
use App\Service\ReservationService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ReservationController
{
    public function __construct(
        private ReservationService $reservationService,
        private ValidatorInterface $validator
    ) {}

    #[Route('/api/reservation', name: 'create_reservation', methods: ['POST'])]
    public function createReservation(Request $request): JsonResponse
    {
        // Deserialize request into DTO
        $data = json_decode($request->getContent(), true);
        $input = new ReservationInput();
        $input->member = $data['member'] ?? null;
        $input->book = $data['book'] ?? null;
        $input->reservationDate = $data['reservation_date'] ?? null;

        // Validate the input
        $errors = $this->validator->validate($input);
        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string) $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            // Create reservation using the service
            $reservation = $this->reservationService->createReservation(
                $input->member,
                $input->book,
                new \DateTime($input->reservationDate)
            );

            // Prepare output DTO
            $output = new ReservationOutput(
                $reservation->getId(),
                $reservation->getMember()->getId(),
                $reservation->getBook()->getId(),
                $reservation->getReservationDate()
            );

            return new JsonResponse($output, JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
