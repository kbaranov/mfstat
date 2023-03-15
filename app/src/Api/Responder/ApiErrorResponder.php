<?php
declare(strict_types=1);

namespace App\Api\Responder;

use App\Api\ApiError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class ApiErrorResponder
{
    public function __construct(
        private readonly SerializerInterface $serializer
    ) {}

    /**
     * @param ApiError[] $error
     * @param array<string, string> $headers
     */
    public function createErrorResponse(array $error, int $code = Response::HTTP_BAD_REQUEST, array $headers = []): JsonResponse
    {
        $json = $this->serializer->serialize($error, JsonEncoder::FORMAT);
        return new JsonResponse($json, $code, $headers, true);
    }
}
