<?php

declare(strict_types=1);

namespace Api\Application\Command;

use Api\Domain\VendingMachine\Aggregate\CoinValue;
use Api\Domain\VendingMachine\Aggregate\ItemCollection;
use Api\Domain\VendingMachine\Aggregate\ItemPrice;
use Api\Domain\VendingMachine\Repository\VendingMachineRepositoryInterface;
use Doctrine\ORM\EntityNotFoundException;
use PHPUnit\Framework\TestCase;
use Tests\Api\Infrastructure\Persistence\InMemoryVendingMachineRepository;
use Tests\Api\Infrastructure\VendingMachine\StubCoin;
use Tests\Api\Infrastructure\VendingMachine\StubItem;
use Tests\Api\Infrastructure\VendingMachine\StubVendingMachine;
use Tests\Api\Infrastructure\VendingMachine\StubVendingMachineId;

final class CollectItemAndCoinsCommandHandlerTest extends TestCase
{
    private CollectItemAndCoinsCommandHandler $sut;
    private VendingMachineRepositoryInterface $vendingMachineRepository;

    protected function setUp(): void
    {
        $this->vendingMachineRepository = new InMemoryVendingMachineRepository();
        $this->sut = new CollectItemAndCoinsCommandHandler($this->vendingMachineRepository);
    }

    public function test_it_can_collect_item_and_coins(): void
    {
        $this->givenAVendingMachineWithVendedItemAndReturnedCoins();

        $this->sut->__invoke(new CollectItemAndCoinsCommand(StubVendingMachineId::DEFAULT_ID));

        $vendingMachine = $this->vendingMachineRepository->findOrFail(StubVendingMachineId::create());

        self::assertNull($vendingMachine->vendedItem());
        self::assertEmpty($vendingMachine->returnedCoins());
    }

    private function givenAVendingMachineWithVendedItemAndReturnedCoins(): void
    {
        $item = StubItem::create(price: ItemPrice::create(0.10));
        $vendingMachine = StubVendingMachine::create(items: ItemCollection::create([$item]));
        $vendingMachine->insertCoin(StubCoin::createRandomWithValue(CoinValue::create(0.10)));
        $vendingMachine->insertCoin(StubCoin::createRandomWithValue(CoinValue::create(0.05)));
        $vendingMachine->vendItem($item->id());

        $this->vendingMachineRepository->save($vendingMachine);
    }

    public function test_it_fails_when_vending_machine_is_not_found(): void
    {
        $this->expectException(EntityNotFoundException::class);

        $this->sut->__invoke(new CollectItemAndCoinsCommand(StubVendingMachineId::DEFAULT_ID));
    }
}