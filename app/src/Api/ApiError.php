<?php
declare(strict_types=1);

namespace App\Api;

use JsonSerializable;
use OpenApi\Annotations as OA;
use ReturnTypeWillChange;
use Stringable;

/**
 * @OA\Schema(
 *     title="Error",
 *     description="Error.",
 *     required={"message"}
 * )
 */
class ApiError implements JsonSerializable
{
    /**
     * @OA\Property(
     *     title="Message",
     *     description="Error message."
     * )
     */
    private string $message;

    /**
     * @OA\Property(
     *     title="Field",
     *     description="The field that the error refers to."
     * )
     */
    private ?string $path;

    /**
     * @OA\Property(
     *     title="Value",
     *     description="The value of the field that the error refers to."
     * )
     */
    private mixed $value;

    /**
     * ErrorType constructor.
     *
     * @param string $message
     * @param string|null $path
     * @param mixed $value
     */
    public function __construct(
        string $message,
        ?string $path = null,
        mixed $value = null
    ) {
        $this->setValue($value);
        $this->setMessage($message);
        $this->setPath($path);
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): ApiError
    {
        $this->message = $message;
        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): ApiError
    {
        $this->path = $path;
        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue(mixed $value): ApiError
    {
        if (null === $value) {
            $this->value = $value;
        } elseif (
            $value instanceof Stringable
            || is_string($value)
            || is_float($value)
            || is_int($value)
        ) {
            $this->value = (string)$value;
        } elseif ($value instanceof JsonSerializable) {
            $this->value = json_encode($value);
        } elseif (is_bool($value)) {
            $this->value = $value ? "TRUE" : "FALSE";
        } else {
            $this->value = serialize($value);
        }
        return $this;
    }

    #[ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
