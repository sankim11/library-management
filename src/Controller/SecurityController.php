<?php

namespace App\Controller;

use App\ApiResource\DTO\SignUpInput;
use App\Entity\Member;
use App\Enum\Role;
use App\Repository\MemberRepository;
use App\Service\MemberService;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;
    private MemberService $memberService;
    private MemberRepository $memberRepository;
    private UserPasswordHasherInterface $passwordHasher;
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(ValidatorInterface $validator, MemberService $memberService, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, MemberRepository $memberRepository, JWTTokenManagerInterface $jwtManager)
    {
        $this->validator = $validator;
        $this->memberService = $memberService;
        $this->entityManager = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->memberRepository = $memberRepository;
        $this->jwtManager = $jwtManager;
    }

    #[Route('/api/sign_in', name: 'sign_in', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return new JsonResponse(['error' => 'Email and password are required.'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->memberRepository->findOneBy(['email' => $email]);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found.'], Response::HTTP_NOT_FOUND);
        }

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['error' => 'Invalid credentials.'], Response::HTTP_UNAUTHORIZED);
        }

        $token = $this->jwtManager->create($user);

        return new JsonResponse([
            'message' => 'Login successful!',
            'user' => [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'role' => $user->getRole(),
            ],
            'token' => $token,
        ], Response::HTTP_OK);
    }

    #[Route('/api/sign_up', name: 'sign_up', methods: ['POST'])]
    public function signUp(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$name || !$email || !$password) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $signUpInput = new SignUpInput();
        $signUpInput->name = $name;
        $signUpInput->email = $email;
        $signUpInput->password = $password;

        $errors = $this->validator->validate($signUpInput);
        if (count($errors) > 0) {
            return new JsonResponse(['error' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $member = $this->entityManager->getRepository(Member::class)->findOneBy(['email' => $email]);
        if ($member) {
            return new JsonResponse(['message' => 'Member already exists', 'member_id' => $member->getId()], Response::HTTP_BAD_REQUEST);
        }

        try {
            $member = new Member();
            $member->setName($name);
            $member->setEmail($email);
            $member->setPassword($this->passwordHasher->hashPassword($member, $password));
            $member->setRole(Role::MEMBER);

            $this->entityManager->persist($member);
            $this->entityManager->flush();

            $token = $this->jwtManager->create($member);

            return new JsonResponse([
                'message' => 'Sign up successful!',
                'user' => [
                    'id' => $member->getId(),
                    'name' => $member->getName(),
                    'email' => $member->getEmail(),
                    'role' => $member->getRole(),
                ],
                'token' => $token,
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
