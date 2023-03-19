<?php
declare(strict_types=1);

namespace App\Api\Controller;

use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Tag(name="HealthCheck")
 */
class HealthCheckController extends ApiController
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
     *     response=503,
     *     description="Don't ready to work."
     * )
     *
     * @return JsonResponse
     */
    public function readinessProbeAction(): JsonResponse
    {
        $checkMessage = 'OK';

        try {
            $result = $this->redis->ping($checkMessage);
        } catch (\Throwable $exception) {
            $response = [
                'message' => 'Redis connection error.',
            ];
            return new JsonResponse($response, Response::HTTP_SERVICE_UNAVAILABLE);
        }

        if ($checkMessage !== $result) {
            $response = [
                'message' => 'Redis not available.',
            ];
            return new JsonResponse($response, Response::HTTP_SERVICE_UNAVAILABLE);
        }

        $response = [
            'message' => 'The application and all services are available.',
        ];

        return new JsonResponse($response, Response::HTTP_OK);
    }
}
