<?php

declare(strict_types=1);

namespace Tests\Api\Infrastructure\Repository;

use Api\Domain\VendingMachine\Aggregate\CoinCollection;
use Api\Domain\VendingMachine\Aggregate\CoinValue;
use Api\Domain\VendingMachine\Aggregate\ItemCollection;
use Api\Domain\VendingMachine\Aggregate\ItemPrice;
use Api\Infrastructure\Persistence\Repository\VendingMachineRepository;
use Shared\Domain\Event\EventBus;
use Shared\Domain\IntValueObject;
use Shared\Domain\StringValueObject;
use Shared\Infrastructure\Persistence\Repository\DoctrineEntityRepository;
use Tests\Api\Infrastructure\VendingMachine\StubCoin;
use Tests\Api\Infrastructure\VendingMachine\StubCoinId;
use Tests\Api\Infrastructure\VendingMachine\StubItem;
use Tests\Api\Infrastructure\VendingMachine\StubItemId;
use Tests\Api\Infrastructure\VendingMachine\StubVendingMachine;
use Tests\Api\Infrastructure\VendingMachine\StubVendingMachineId;

final class VendingMachineRepositoryTest extends RepositoryTestCase
{
    public function test_it_should_save_and_find_a_vending_machine(): void
    {
        $items = ItemCollection::create([
            StubItem::create(
                StubItemId::create(),
                new StringValueObject(StubItem::DEFAULT_NAME),
                ItemPrice::create(0.05),
                new IntValueObject(10)
            ),
            StubItem::create(
                StubItemId::createOther(),
                new StringValueObject('Juice'),
                ItemPrice::create(1),
                new IntValueObject(5)
            ),
        ]);

        $coins = CoinCollection::create([StubCoin::create(value: CoinValue::create(0.05))]);

        $vendingMachine = StubVendingMachine::create(items: $items, coins: $coins);
        $vendingMachine->insertCoin(StubCoin::create(StubCoinId::createOther(), CoinValue::create(0.10)));
        $vendingMachine->vendItem(new StringValueObject(StubItem::DEFAULT_NAME));

        $this->repository->save($vendingMachine);

        $vendingMachineFound = $this->repository->findOrFail(StubVendingMachineId::create());

        self::assertEquals($vendingMachine->id(), $vendingMachineFound->id());
        self::assertEquals($vendingMachine->name(), $vendingMachineFound->name());
        self::assertEquals($vendingMachine->items(), $vendingMachineFound->items());
        self::assertEquals($vendingMachine->coins(), $vendingMachineFound->coins());
    }

    protected function repository(): DoctrineEntityRepository
    {
        return new VendingMachineRepository($this->em, $this->createMock(EventBus::class));
    }

    protected function arrange(): void
    {
        $this->purgeTables('vending_machine');
        $this->purgeTables('vending_machine_item');
        $this->purgeTables('vending_machine_coin');
    }
}
