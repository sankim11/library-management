<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
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

        // Check if the user is an instance of Member
        if (!$user instanceof \App\Entity\Member) {
            return new JsonResponse(['error' => 'Invalid user'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Return a success response
        return new JsonResponse([
            'message' => 'Login successful!',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ],
        ], JsonResponse::HTTP_OK);
    }
}
