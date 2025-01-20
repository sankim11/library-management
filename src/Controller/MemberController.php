<?php

namespace App\Controller;

use App\Repository\MemberRepository;
use App\Service\MemberService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MemberController
{
    private MemberService $memberService;
    private MemberRepository $memberRepository;

    public function __construct(MemberService $memberService, MemberRepository $memberRepository)
    {
        $this->memberService = $memberService;
        $this->memberRepository = $memberRepository;
    }

    #[Route('/member', name: 'create_member', methods: ['POST'])]
    public function createMember(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $name = $data['name'] ?? null;
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $role = $data['role'] ?? null;

        if (!$name || !$email || !$password || !$role) {
            return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $member = $this->memberService->createMember($name, $email, $password, $role);
            return new JsonResponse([
                'message' => 'Member created successfully!',
                'member_id' => $member->getId(),
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/members', name: 'get_all_members', methods: ['GET'])]
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

    #[Route('/member/{id}', name: 'get_member', methods: ['GET'])]
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

    #[Route('/member/{id}', name: 'delete_member', methods: ['DELETE'])]
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
