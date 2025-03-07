<?php

declare(strict_types=1);

namespace Shared\Common\Exception;

use Throwable;

abstract class Exception extends \Exception
{
    public static function create(string $message, Throwable $previous = null): self
    {
        return new static($message, 0, $previous);
    }
}
