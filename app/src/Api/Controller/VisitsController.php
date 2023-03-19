<?php
declare(strict_types=1);

namespace App\Api\Controller;

use App\Api\Request\PostVisitsDto;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Tag(name="Visits")
 */
class VisitsController extends ApiController
{
    public const VISITS_STORAGE_KEY = 'visits';
    public const VISITS_CACHE_STORAGE_KEY = 'visits_cache';

    const COUNTRIES = [
        "AE" => "United Arab Emirates",
        "AU" => "Australia",
        "BR" => "Brazil",
        "CA" => "Canada",
        "CN" => "China",
        "DE" => "Germany",
        "ES" => "Spain",
        "FR" => "France",
        "GB" => "United Kingdom",
        "ID" => "Indonesia",
        "IN" => "India",
        "IT" => "Italy",
        "JP" => "Japan",
        "KR" => "South Korea",
        "MX" => "Mexico",
        "MY" => "Malaysia",
        "NL" => "Netherlands",
        "PH" => "Philippines",
        "PK" => "Pakistan",
        "RU" => "Russia",
        "SA" => "Saudi Arabia",
        "TH" => "Thailand",
        "TR" => "Turkey",
        "TW" => "Taiwan",
        "UA" => "Ukraine",
        "US" => "United States",
        "VN" => "Vietnam",
        "ZA" => "South Africa",
    ];

    /**
     * @Route(
     *     path="/visits",
     *     name="Get visit statistics",
     *     methods={"GET"}
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="Visit statistics."
     * )
     *
     * @OA\Response(
     *     response=500,
     *     description="Error of getting visit statistics."
     * )
     *
     * @return JsonResponse
     */
    public function getAction(): JsonResponse
    {
        try {
            $data = json_decode($this->redis->get(self::VISITS_CACHE_STORAGE_KEY), true);
        } catch (\Throwable $exception) {
            $this->logger->error('Error while getting data from Redis', [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'code' => $exception->getCode(),
            ]);
            $data = [
                'message' => 'Error while getting data.',
            ];
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return new JsonResponse($data, $status ?? Response::HTTP_OK);
    }

    /**
     * @Route(
     *     path="/visits",
     *     name="Store the visit",
     *     methods={"POST"}
     * )
     *
     * @OA\RequestBody(
     *    required=true,
     *    request="PostVisitsDto",
     *    @OA\JsonContent(ref=@Model(type=PostVisitsDto::class))
     * )
     *
     * @OA\Response(
     *     response=201,
     *     description="The visit stored successed."
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="There is invalid parameter in request."
     * )
     *
     * @OA\Response(
     *     response=500,
     *     description="Error of storing the visit."
     * )
     *
     * @param PostVisitsDto $dto
     * @return JsonResponse
     */
    public function postAction(PostVisitsDto $dto): JsonResponse
    {
        $country = $dto->getCountry();

        if (!key_exists($country, self::COUNTRIES)) {
            $data = [
                'message' => 'Validation errors in your request.',
                'errors' => [
                    [
                        'message' => 'The value is invalid.',
                        'field' => 'country',
                    ],
                ]
            ];
            return new JsonResponse($data, Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->redis->hincrby(self::VISITS_STORAGE_KEY, $country, 1);
        } catch (\Throwable $exception) {
            $this->logger->error('Error while getting data from Redis', [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'code' => $exception->getCode(),
            ]);
            $data = [
                'message' => 'Error while storing data.',
            ];
            return new JsonResponse($data, Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $data = [
            'message' => 'The visit from ' . $country . ' was stored successfully.',
        ];

        return new JsonResponse($data, Response::HTTP_CREATED);
    }
}