<?php

namespace App\Controller;

use App\Entity\Member;
use App\Enum\Role;
use App\Repository\MemberRepository;
use App\Service\MemberService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MemberController
{
    private EntityManagerInterface $entityManager;
    private MemberService $memberService;
    private MemberRepository $memberRepository;

    public function __construct(EntityManagerInterface $entityManager, MemberService $memberService, MemberRepository $memberRepository)
    {
        $this->entityManager = $entityManager;
        $this->memberService = $memberService;
        $this->memberRepository = $memberRepository;
    }
    #[Route('/api/create_member', name: 'create_member', methods: ['POST'])]
    public function createMember(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $name = $data['name'];
        $email = $data['email'];
        $password = $data['password'];
        $role = $data['role'];
        $roleInput = Role::from($role);

        if (!$name || !$email || !$roleInput) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        $member = $this->entityManager->getRepository(Member::class)->findOneBy(['email' => $email]);

        if ($member) {
            return new JsonResponse(['message' => 'Member already exists', 'member_id' => $member->getId()], Response::HTTP_BAD_REQUEST);
        }

        try {
            $member = $this->memberService->createMember($name, $email, $password, $roleInput);
            return new JsonResponse([
                'message' => 'Member created successfully!',
                'member_id' => $member->getId(),
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/api/get_all_members', name: 'get_all_members', methods: ['GET'])]
    public function getAllMembers(): JsonResponse
    {
        $members = $this->memberRepository->findAll();

        $response = array_map(fn($member) => [
            'id' => $member->getId(),
            'name' => $member->getName(),
            'email' => $member->getEmail(),
            'role' => $member->getRole()->value,
        ], $members);

        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('/api/get_member/{id}', name: 'get_member', methods: ['GET'])]
    public function getMember(int $id): JsonResponse
    {
        $member = $this->memberRepository->find($id);

        if (!$member) {
            return new JsonResponse(['error' => 'Member not found'], Response::HTTP_NOT_FOUND);
        }

        $response = [
            'id' => $member->getId(),
            'name' => $member->getName(),
            'email' => $member->getEmail(),
            'role' => $member->getRole()->value,
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }

    #[Route('/api/update_member/{id}', name: 'update_member', methods: ['PUT'])]
    public function updateMember(int $id, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->memberService->updateMember($id, $data);
        return new JsonResponse(['message' => 'Member updated successfully'], Response::HTTP_OK);
    }

    #[Route('/api/delete_member/{id}', name: 'delete_member', methods: ['DELETE'])]
    public function deleteMember(int $id): JsonResponse
    {
        try {
            $this->memberService->deleteMember($id);
            return new JsonResponse(['message' => 'Member deleted successfully'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
