<?php

declare(strict_types=1);

namespace Tests\Api\Infrastructure\VendingMachine;

use Api\Domain\VendingMachine\Aggregate\CoinCollection;
use Api\Domain\VendingMachine\Aggregate\ItemCollection;
use Api\Domain\VendingMachine\Aggregate\VendingMachine;
use Api\Domain\VendingMachine\Aggregate\VendingMachineId;

final class StubVendingMachine
{
    public static function create(
        ?VendingMachineId $vendingMachineId = null,
        ?ItemCollection $items = null,
        ?CoinCollection $coins = null
    ): VendingMachine {
        return VendingMachine::create(
            $vendingMachineId ?? StubVendingMachineId::create(),
            $items ?? ItemCollection::createEmpty(),
            $coins ?? CoinCollection::createEmpty()
        );
    }
}
