<?php
declare(strict_types=1);

namespace App\Event;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\EventDispatcher\Event;

class BeforeDtoValidationEvent extends Event
{
    public const NAME = 'BeforeDtoValidationEvent';

    /** @var string[] */
    private array $validationGroups = [];

    public function __construct(
        private readonly Request $request,
        private readonly object  $dto
    ) {}

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getDto(): object
    {
        return $this->dto;
    }

    /** @return string[] */
    public function getValidationGroups(): array
    {
        return $this->validationGroups;
    }

    /** @param string[] $validationGroups */
    public function setValidationGroups(array $validationGroups): void
    {
        $this->validationGroups = $validationGroups;
        $this->stopPropagation();
    }
}
