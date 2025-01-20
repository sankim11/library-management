<?php

namespace App\Controller;

use App\Exception\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SecurityController
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        $user = $this->tokenStorage->getToken()->getUser();

        // Check if user is instance of meber
        if (!$user instanceof \App\Entity\Member) {
            throw new ValidationException('Invalid user credentials.', Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
            'message' => 'Login successful!',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ],
        ], Response::HTTP_OK);
    }
}
