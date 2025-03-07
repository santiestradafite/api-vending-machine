<?php

declare(strict_types=1);

namespace Api\Domain\Aggregate;

use Shared\Domain\FloatValueObject;
use Assert\Assertion;

/**
 * @method CoinId id()
 */
final class CoinValue extends FloatValueObject
{
    private const ALLOWED_VALUES = [
        0.05,
        0.10,
        0.25,
        1.00
    ];

    private function __construct(float $value)
    {
        Assertion::choice($value, self::ALLOWED_VALUES);
        parent::__construct($value);
    }

    public static function create(float $value): self
    {
        return new self($value);
    }
}