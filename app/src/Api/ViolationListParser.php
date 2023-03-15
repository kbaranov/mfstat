<?php
declare(strict_types=1);

namespace App\Api;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ViolationListParser
{
    /**
     * @param ConstraintViolationListInterface $list
     * @return ApiError[]
     */
    public function parseToApiErrorList(ConstraintViolationListInterface $list): array
    {
        $errors = [];
        foreach ($list as $item) {
            $errors[] = new ApiError(
                (string) $item->getMessage(),
                $item->getPropertyPath(),
                $item->getInvalidValue()
            );
        }

        return $errors;
    }
}
