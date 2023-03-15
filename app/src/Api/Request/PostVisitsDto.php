<?php
declare(strict_types=1);

namespace App\Api\Request;

class PostVisitsDto implements BodyRequestDtoMarker
{
    public function __construct(
        private readonly string $country
    ) {}

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }
}