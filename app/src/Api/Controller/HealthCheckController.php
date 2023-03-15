<?php
declare(strict_types=1);

namespace App\Api\Controller;

use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Tag(name="HealthCheck")
 */
class HealthCheckController extends AbstractController
{
    /**
     * @Route(
     *     path="/readiness",
     *     name="Readiness probe",
     *     methods={"GET"}
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="Ready to work."
     * )
     *
     * @OA\Response(
     *     response=500,
     *     description="Don't ready to work."
     * )
     *
     * @return JsonResponse
     */
    public function readinessProbeAction(): JsonResponse
    {
        return new JsonResponse();
    }
}
