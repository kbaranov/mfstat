<?php
declare(strict_types=1);

namespace App\Api\Exception;

use App\Api\ApiError;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class ApiException extends Exception
{
    /**
     * @var ApiError[]
     */
    private array $validationErrors;

    /**
     * ApiValidatorException constructor.
     *
     * @param ApiError[] $validationErrors
     * @param int $code
     */
    public function __construct(array $validationErrors, int $code = Response::HTTP_BAD_REQUEST)
    {
        parent::__construct("apiValidatorException", $code);
        $this->validationErrors = $validationErrors;
    }

    /**
     * @return ApiError[]
     */
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }
}
