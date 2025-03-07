<?php

declare(strict_types=1);

namespace Shared\Domain;

class IntValueObject
{
    protected int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function isGreaterThanZero(): bool
    {
        return $this->value() > 0;
    }

    public function decrease(): self
    {
        return new self($this->value - 1);
    }
}
