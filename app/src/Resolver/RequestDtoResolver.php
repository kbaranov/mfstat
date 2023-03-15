<?php
declare(strict_types=1);

namespace App\Resolver;

use App\Api\ApiError;
use App\Api\Exception\ApiException;
use App\Api\Request\BodyRequestDtoMarker;
use App\Event\BeforeDtoValidationEvent;
use App\Resolver\Formatter\FormatterProvider;
use App\Api\ViolationListParser;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestDtoResolver implements ArgumentValueResolverInterface
{
    public function __construct(
        private readonly SerializerInterface      $serializer,
        private readonly ValidatorInterface       $validator,
        private readonly ViolationListParser      $violationListParser,
        private readonly FormatterProvider        $formatterProvider,
        private readonly EventDispatcherInterface $dispatcher
    ) {}

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return is_subclass_of((string)$argument->getType(), BodyRequestDtoMarker::class);
    }

    /**
     * @return \Generator<BodyRequestDtoMarker>
     * @throws ApiException
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $content = $request->getContent();

        try {
            $dto = $this->serializer->deserialize($content, (string)$argument->getType(), JsonEncoder::FORMAT);
        } catch (ExceptionInterface $exception) {
            throw new ApiException([new ApiError("Invalid JSON")]);
        }

        $event = new BeforeDtoValidationEvent($request, $dto);
        $this->dispatcher->dispatch($event, BeforeDtoValidationEvent::NAME);
        $groups = $event->getValidationGroups();

        $violationList = $this->validator->validate($dto, null, $groups);
        if ($violationList->count() > 0) {
            $errors = $this->violationListParser->parseToApiErrorList($violationList);
            throw new ApiException($errors);
        }

        $this->formatterProvider->format($dto);

        yield $dto;
    }
}
