<?php

namespace App\Controller;

use App\Service\DashboardService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController
{
    private DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    #[Route('/api/dashboard/stats', name: 'dashboard_stats', methods: ['GET'])]
    public function getStats(Request $request): JsonResponse
    {
        $memberId = $request->query->get('user_id');

        if (!$memberId) {
            return new JsonResponse(['error' => 'Member ID is required'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $stats = $this->dashboardService->getDashboardStats($memberId);
            return new JsonResponse($stats, Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
