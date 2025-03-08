<?php

declare(strict_types=1);

namespace Api\Domain\VendingMachine\Aggregate;

use Assert\InvalidArgumentException;
use Shared\Domain\FloatValueObject;

/**
 * @method CoinId id()
 */
final class ItemPrice extends FloatValueObject
{
    private const ALLOWED_COIN_VALUES = [
        0.05,
        0.10,
        0.25,
        1.00
    ];

    private function __construct(float $value)
    {
        if (!$this->isValidPrice($value)) {
            throw new InvalidArgumentException(
                sprintf('Item must be able to be sold using <%s> coins', implode(', ', self::ALLOWED_COIN_VALUES)),
                0
            );
        }

        parent::__construct($value);
    }

    public static function create(float $value): self
    {
        return new self($value);
    }

    private function isValidPrice(float $value): bool
    {
        return $this->canBeSumOfAllowedValues($value);
    }

    private function canBeSumOfAllowedValues(float $value): bool
    {
        if ($value === 0.0) {
            return true;
        }

        foreach (self::ALLOWED_COIN_VALUES as $allowedValue) {
            if ($value >= $allowedValue && $this->canBeSumOfAllowedValues(round($value - $allowedValue, 2))) {
                return true;
            }
        }

        return false;
    }
}