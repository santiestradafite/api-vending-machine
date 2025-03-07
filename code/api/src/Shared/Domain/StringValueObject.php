<?php

declare(strict_types=1);

namespace Shared\Domain;

class StringValueObject
{
    protected string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equalsTo(StringValueObject $other): bool
    {
        return $this->value === $other->value() && get_class($this) === get_class($other);
    }
}
