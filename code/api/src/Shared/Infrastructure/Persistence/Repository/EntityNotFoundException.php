<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Persistence\Repository;

use Shared\Common\Exception\Exception;
use Shared\Domain\Uuid;
use Throwable;

class EntityNotFoundException extends Exception
{
    final public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function forId(Uuid $id): static
    {
        $shortClassName = self::getShortClassName($id);

        return new static(sprintf('Aggregate of id %s of type %s was not found', $id->value(), $shortClassName));
    }

    private static function getShortClassName(Uuid $id): string
    {
        $classNameExploded = explode("\\", $id::class);

        return array_pop($classNameExploded);
    }
}
