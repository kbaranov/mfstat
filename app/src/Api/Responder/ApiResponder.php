<?php
declare(strict_types=1);

namespace App\Api\Responder;

use App\Api\Exception\ApiException;
use App\Api\ViolationListParser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiResponder
{
    public function __construct(
        private readonly Serializer          $serializer,
        private readonly ValidatorInterface  $validator,
        private readonly ViolationListParser $violationListParser,
        private readonly string              $env
    ) {}

    /**
     * @param mixed|null $response
     * @throws ApiException
     */
    public function createResponse(mixed $response = null, int $status = Response::HTTP_OK): JsonResponse
    {
        if ($this->env !== 'prod' && $response !== null) {
            $validationResult = $this->validator->validate($response);
            if ($validationResult->count() > 0) {
                throw new ApiException($this->violationListParser->parseToApiErrorList($validationResult));
            }
        }

        $needSerialization = (null !== $response);

        if ($needSerialization) {
            $response = $this->serializer->serialize($response, JsonEncoder::FORMAT);
        }

        return new JsonResponse($response, $status, [], $needSerialization);
    }
}
