<?php

declare(strict_types=1);

namespace Tests\Api\Application\Command;

use Api\Application\Command\VendItemCommand;
use Api\Application\Command\VendItemCommandHandler;
use Api\Domain\VendingMachine\Aggregate\CoinValue;
use Api\Domain\VendingMachine\Aggregate\Item;
use Api\Domain\VendingMachine\Aggregate\ItemCollection;
use Api\Domain\VendingMachine\Aggregate\ItemPrice;
use Api\Domain\VendingMachine\Repository\VendingMachineRepositoryInterface;
use Doctrine\ORM\EntityNotFoundException;
use PHPUnit\Framework\TestCase;
use Tests\Api\Infrastructure\Persistence\InMemoryVendingMachineRepository;
use Tests\Api\Infrastructure\VendingMachine\StubCoin;
use Tests\Api\Infrastructure\VendingMachine\StubItem;
use Tests\Api\Infrastructure\VendingMachine\StubItemId;
use Tests\Api\Infrastructure\VendingMachine\StubVendingMachine;
use Tests\Api\Infrastructure\VendingMachine\StubVendingMachineId;

final class VendItemCommandHandlerTest extends TestCase
{
    private VendItemCommandHandler $sut;
    private VendingMachineRepositoryInterface $vendingMachineRepository;

    protected function setUp(): void
    {
        $this->vendingMachineRepository = new InMemoryVendingMachineRepository();
        $this->sut = new VendItemCommandHandler($this->vendingMachineRepository);
    }

    public function test_it_can_vend_an_item(): void
    {
        $item = StubItem::create(price: ItemPrice::create(0.10));
        $this->givenAVendingMachineWithEnoughInsertedCoinsAndItem($item);

        $this->sut->__invoke(new VendItemCommand(StubVendingMachineId::DEFAULT_ID, StubItemId::DEFAULT_ID));

        $vendingMachine = $this->vendingMachineRepository->findOrFail(StubVendingMachineId::create());

        self::assertEquals($item->id(), $vendingMachine->vendedItem()->id());
    }

    private function givenAVendingMachineWithEnoughInsertedCoinsAndItem(Item $item): void
    {
        $vendingMachine = StubVendingMachine::create(items: ItemCollection::create([$item]));
        $vendingMachine->insertCoin(StubCoin::createRandomWithValue(CoinValue::create(0.10)));

        $this->vendingMachineRepository->save($vendingMachine);
    }

    public function test_it_fails_when_vending_machine_is_not_found(): void
    {
        $this->expectException(EntityNotFoundException::class);

        $this->sut->__invoke(new VendItemCommand(StubVendingMachineId::DEFAULT_ID, StubItemId::DEFAULT_ID));
    }
}