<?php

declare(strict_types=1);

namespace Api\Application\Command;

use Api\Domain\VendingMachine\Aggregate\Coin;
use Api\Domain\VendingMachine\Aggregate\CoinCollection;
use Api\Domain\VendingMachine\Aggregate\CoinId;
use Api\Domain\VendingMachine\Aggregate\CoinValue;
use Api\Domain\VendingMachine\Aggregate\Item;
use Api\Domain\VendingMachine\Aggregate\ItemCollection;
use Api\Domain\VendingMachine\Aggregate\ItemId;
use Api\Domain\VendingMachine\Aggregate\ItemPrice;
use Api\Domain\VendingMachine\Aggregate\VendingMachine;
use Api\Domain\VendingMachine\Aggregate\VendingMachineId;
use Api\Domain\VendingMachine\Repository\VendingMachineRepositoryInterface;
use Shared\Domain\Command\CommandHandler;
use Shared\Domain\IntValueObject;
use Shared\Domain\StringValueObject;

final class SetupVendingMachineCommandHandler implements CommandHandler
{
    public function __construct(private readonly VendingMachineRepositoryInterface $vendingMachineRepository)
    {
    }

    public function __invoke(SetupVendingMachineCommand $command): void
    {
        $items = ItemCollection::create([
            Item::create(
                ItemId::generate(),
                new StringValueObject('Water'),
                ItemPrice::create(0.65),
                new IntValueObject(10)
            ),
            Item::create(
                ItemId::generate(),
                new StringValueObject('Juice'),
                ItemPrice::create(1),
                new IntValueObject(5)
            ),
            Item::create(
                ItemId::generate(),
                new StringValueObject('Soda'),
                ItemPrice::create(1.5),
                new IntValueObject(1)
            )
        ]);

        $coins = CoinCollection::create([
            Coin::create(CoinId::generate(), CoinValue::create(0.05)),
            Coin::create(CoinId::generate(), CoinValue::create(0.10)),
            Coin::create(CoinId::generate(), CoinValue::create(0.25)),
            Coin::create(CoinId::generate(), CoinValue::create(1))
        ]);

        $vendingMachine = VendingMachine::create(
            VendingMachineId::fromString($command->vendingMachineId()),
            new StringValueObject('Vending machine'),
            $items,
            $coins
        );

        $this->vendingMachineRepository->save($vendingMachine);
    }
}