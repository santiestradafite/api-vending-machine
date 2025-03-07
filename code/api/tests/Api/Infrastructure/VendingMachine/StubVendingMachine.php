<?php

declare(strict_types=1);

namespace Tests\Api\Infrastructure\VendingMachine;

use Api\Domain\Aggregate\CoinCollection;
use Api\Domain\Aggregate\ItemCollection;
use Api\Domain\Aggregate\VendingMachine;
use Api\Domain\Aggregate\VendingMachineId;

final class StubVendingMachine
{
    public static function create(
        ?VendingMachineId $vendingMachineId = null,
        ?ItemCollection $items = null,
        ?CoinCollection $availableCoins = null
    ): VendingMachine {
        return VendingMachine::create(
            $vendingMachineId ?? StubVendingMachineId::create(),
            $items ?? ItemCollection::createEmpty(),
            $availableCoins ?? CoinCollection::createEmpty()
        );
    }
}
