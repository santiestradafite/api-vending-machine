<?php

declare(strict_types=1);

namespace Shared\Domain;

final class BoolValueObject
{
    private const NUMBER_FALSE = 0;
    private const NUMBER_TRUE  = 1;

    protected bool $value;

    public function __construct(bool $value)
    {
        $this->value = $value;
    }

    public static function create(bool $value): self
    {
        return new self($value);
    }

    public static function true(): self
    {
        return new self(true);
    }

    public static function false(): self
    {
        return new self(false);
    }

    public function value(): bool
    {
        return $this->value;
    }

    public function isTrue(): bool
    {
        return $this->value === true;
    }

    public function isFalse(): bool
    {
        return $this->value === false;
    }

    public function toNumber(): int
    {
        return $this->value ? self::NUMBER_TRUE : self::NUMBER_FALSE;
    }
}
