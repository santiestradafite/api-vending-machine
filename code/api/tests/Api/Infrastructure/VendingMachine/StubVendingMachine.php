<?php

declare(strict_types=1);

namespace Tests\Api\Infrastructure\VendingMachine;

use Api\Domain\VendingMachine\Aggregate\CoinCollection;
use Api\Domain\VendingMachine\Aggregate\ItemCollection;
use Api\Domain\VendingMachine\Aggregate\VendingMachine;
use Api\Domain\VendingMachine\Aggregate\VendingMachineId;
use Shared\Domain\StringValueObject;

final class StubVendingMachine
{
    public const DEFAULT_NAME = 'Test vending machine';

    public static function create(
        ?VendingMachineId $vendingMachineId = null,
        ?StringValueObject $name = null,
        ?ItemCollection $items = null,
        ?CoinCollection $coins = null
    ): VendingMachine {
        return VendingMachine::create(
            $vendingMachineId ?? StubVendingMachineId::create(),
            $name ?? new StringValueObject(self::DEFAULT_NAME),
            $items ?? ItemCollection::createEmpty(),
            $coins ?? CoinCollection::createEmpty()
        );
    }
}
