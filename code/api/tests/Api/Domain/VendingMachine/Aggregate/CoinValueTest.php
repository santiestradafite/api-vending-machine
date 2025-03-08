<?php

declare(strict_types=1);

namespace Api\Domain\VendingMachine\Aggregate;

use Assert\AssertionFailedException;
use PHPUnit\Framework\TestCase;

final class CoinValueTest extends TestCase
{
    /** @dataProvider allowedValues */
    public function test_it_can_create_a_coin_value(float $allowedValue): void
    {
        $sut = CoinValue::create($allowedValue);

        self::assertEquals($allowedValue, $sut->value());
    }

    public function allowedValues(): array
    {
        return [
            [0.05],
            [0.10],
            [0.1],
            [0.25],
            [1],
            [1.00]
        ];
    }

    public function test_it_fails_when_coin_value_is_not_allowed(): void
    {
        $this->expectException(AssertionFailedException::class);

        CoinValue::create(2.50);
    }
}