<?php
declare(strict_types=1);

namespace App\Api\Controller;

use App\Api\Request\PostVisitsDto;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Tag(name="Visits")
 */
class VisitsController extends AbstractController
{
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
        $response = [];

        // TODO: Extract data from Redis
        $data = ["US" => 1000, "UK" => 2000,"CA" => 3000];

        foreach ($data as $country => $quantity) {
            $response[$country] = $quantity;
        }

        return new JsonResponse($response, Response::HTTP_OK);
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
     *     response=200,
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

        if ($country === null) {
            $response = [
                'message' => 'Validation errors in your request.',
                'errors' => [
                    [
                        'message' => 'Oops! The value is invalid.',
                        'field' => 'country',
                    ],
                ]
            ];

            return new JsonResponse($response, Response::HTTP_BAD_REQUEST);
        }

        // TODO: Insert data to Redis

        $response = [
            'message' => 'The visit from ' . $country . ' was stored successfully.',
        ];

        return new JsonResponse($response, Response::HTTP_CREATED);
    }
}