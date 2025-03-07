<?php

declare(strict_types=1);

namespace Shared\Common\Exception;

use Exception;
use Throwable;

final class TypedCollectionException extends Exception
{
    public static function create(string $message, Throwable $previous = null): self
    {
        return new self($message, 0, $previous);
    }
}
