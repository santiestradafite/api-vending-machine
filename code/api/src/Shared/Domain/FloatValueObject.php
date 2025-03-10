<?php

declare(strict_types=1);

namespace Shared\Domain;

class FloatValueObject
{
    public function __construct(protected readonly float $value)
    {
    }

    public function value(): float
    {
        return round($this->value, 2);
    }

    public function add(FloatValueObject $other): FloatValueObject
    {
        return new static($this->value() + $other->value());
    }

    public function subtract(FloatValueObject $other): FloatValueObject
    {
        return new static($this->value() - $other->value());
    }

    public function isGreaterThan(FloatValueObject $other): bool
    {
        return $this->value() > $other->value();
    }

    public function isGreaterThanZero(): bool
    {
        return $this->value() > 0;
    }

    public function isGreaterThanOrEqualsTo(FloatValueObject $other): bool
    {
        return $this->value() >= $other->value();
    }

    public static function zero(): self
    {
        return new self(0);
    }
}
