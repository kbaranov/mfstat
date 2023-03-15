<?php
declare(strict_types=1);

namespace App\Resolver\Formatter;

use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class FormatterProvider implements ServiceSubscriberInterface, FormatterInterface
{
    public function __construct(
        private readonly ContainerInterface $locator
    ) {}

    public static function getSubscribedServices(): array
    {
        return [];
    }

    public function format(object $dto): void
    {
        $class = get_class($dto);
        if ($this->locator->has($class)) {
            /** @var FormatterInterface $formatter */
            $formatter = $this->locator->get($class);
            $formatter->format($dto);
        }
    }
}
