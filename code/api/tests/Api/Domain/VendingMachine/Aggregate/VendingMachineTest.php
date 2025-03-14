<?php

declare(strict_types=1);

namespace Tests\Api\Domain\VendingMachine\Aggregate;

use Api\Domain\VendingMachine\Aggregate\CoinCollection;
use Api\Domain\VendingMachine\Aggregate\CoinValue;
use Api\Domain\VendingMachine\Aggregate\ItemCollection;
use Api\Domain\VendingMachine\Aggregate\ItemPrice;
use Api\Domain\VendingMachine\Aggregate\VendingMachine;
use Api\Domain\VendingMachine\Event\CoinInsertedEvent;
use Api\Domain\VendingMachine\Event\ItemVendedEvent;
use Api\Domain\VendingMachine\Event\VendingMachineCreatedEvent;
use Api\Domain\VendingMachine\Exception\ItemNotVendedException;
use PHPUnit\Framework\TestCase;
use Shared\Common\Collection;
use Shared\Domain\AggregateRoot;
use Shared\Domain\Event\DomainEvent;
use Shared\Domain\FloatValueObject;
use Shared\Domain\IntValueObject;
use Shared\Domain\StringValueObject;
use Tests\Api\Infrastructure\VendingMachine\StubCoin;
use Tests\Api\Infrastructure\VendingMachine\StubItem;
use Tests\Api\Infrastructure\VendingMachine\StubItemId;
use Tests\Api\Infrastructure\VendingMachine\StubVendingMachine;
use Tests\Api\Infrastructure\VendingMachine\StubVendingMachineId;

final class VendingMachineTest extends TestCase
{
    public function test_it_can_create_a_vending_machine(): void
    {
        $id = StubVendingMachineId::create();
        $name = new StringValueObject(StubVendingMachine::DEFAULT_NAME);
        $items = ItemCollection::create([StubItem::create()]);
        $coins = CoinCollection::create([StubCoin::create()]);

        $sut = VendingMachine::create($id, $name, $items, $coins);

        self::assertEquals($id, $sut->id());
        self::assertEquals($name, $sut->name());
        self::assertEquals(ItemCollection::cloneIndexed($items), $sut->items());
        self::assertEquals(CoinCollection::cloneIndexed($coins), $sut->coins());
        self::assertEmpty($sut->insertedCoins());
        self::assertEmpty($sut->returnedCoins());
        self::assertNull($sut->vendedItem());

        $this->thenEventOfClassNameHasBeenRecorded($sut,VendingMachineCreatedEvent::class);
    }

    public function test_it_can_insert_a_coin(): void
    {
        $sut = StubVendingMachine::create(coins: CoinCollection::create([StubCoin::create()]));
        $coin = StubCoin::createRandomWithValue(CoinValue::create(0.25));
        $sut->insertCoin($coin);

        self::assertCount(1, $sut->insertedCoins());
        self::assertCount(2, $sut->coins());
        self::assertEmpty($sut->returnedCoins());
        self::assertEquals($sut, $coin->vendingMachine());

        $this->thenEventOfClassNameHasBeenRecorded($sut,CoinInsertedEvent::class);
    }

    public function test_it_can_return_inserted_coins(): void
    {
        $sut = StubVendingMachine::create(coins: CoinCollection::create([StubCoin::create()]));
        $sut->insertCoin(StubCoin::createRandomWithValue(CoinValue::create(0.25)));
        $sut->insertCoin(StubCoin::createRandomWithValue(CoinValue::create(0.05)));

        $sut->returnInsertedCoins();

        self::assertCount(2, $sut->returnedCoins());
        self::assertEmpty($sut->insertedCoins());
        self::assertCount(3, $sut->coins());
    }

    public function test_it_can_vend_an_item_with_exact_price(): void
    {
        $item = StubItem::create();
        $sut = StubVendingMachine::create(
            items: ItemCollection::create([$item]),
            coins: CoinCollection::create([StubCoin::create()])
        );
        $sut->insertCoin(StubCoin::createRandomWithValue(CoinValue::create(0.25)));
        $sut->insertCoin(StubCoin::createRandomWithValue(CoinValue::create(0.25)));
        $sut->insertCoin(StubCoin::createRandomWithValue(CoinValue::create(0.25)));

        $sut->vendItem($item->id());

        self::assertEquals($sut->vendedItem(), $item);
        self::assertEquals(new IntValueObject(2), $item->stock());
        self::assertEmpty($sut->insertedCoins());
        self::assertEmpty($sut->returnedCoins());
        self::assertCount(4, $sut->coins());

        $this->thenEventOfClassNameHasBeenRecorded($sut,ItemVendedEvent::class);
    }

    public function test_it_can_vend_an_item_and_return_change(): void
    {
        $coin = StubCoin::create();
        $item = StubItem::create(price: ItemPrice::create(0.10));
        $sut = StubVendingMachine::create(
            items: ItemCollection::create([$item]),
            coins: CoinCollection::create([$coin])
        );
        $insertedCoin1 = StubCoin::createRandomWithValue(CoinValue::create(1));
        $insertedCoin2 = StubCoin::createRandomWithValue(CoinValue::create(0.25));
        $insertedCoin3 = StubCoin::createRandomWithValue(CoinValue::create(0.10));
        $insertedCoin4 = StubCoin::createRandomWithValue(CoinValue::create(0.05));
        $sut->insertCoin($insertedCoin1);
        $sut->insertCoin($insertedCoin2);
        $sut->insertCoin($insertedCoin3);
        $sut->insertCoin($insertedCoin4);

        $sut->vendItem($item->id());

        self::assertEquals($sut->vendedItem(), $item);
        self::assertEquals(new IntValueObject(2), $item->stock());
        self::assertEmpty($sut->insertedCoins());
        self::assertCount(3, $sut->returnedCoins());
        self::assertContains($insertedCoin1, $sut->returnedCoins());
        self::assertContains($insertedCoin2, $sut->returnedCoins());
        self::assertContains($coin, $sut->returnedCoins());
        self::assertCount(5, $sut->coins());

        $this->thenEventOfClassNameHasBeenRecorded($sut,ItemVendedEvent::class);
    }

    public function test_it_should_fail_when_trying_to_vend_an_item_without_collecting_the_already_vended_one(): void
    {
        $sut = StubVendingMachine::create(
            items: ItemCollection::create([StubItem::create(price: ItemPrice::create(0.1))])
        );
        $sut->insertCoin(StubCoin::createRandomWithValue(CoinValue::create(0.1)));
        $sut->vendItem(StubItemId::create());

        $this->expectExceptionObject(ItemNotVendedException::becauseVendedItemIsNotCollected());

        $sut->vendItem(StubItemId::create());
    }

    public function test_it_should_fail_when_trying_to_vend_a_non_existing_item(): void
    {
        $sut = StubVendingMachine::create(items: ItemCollection::create([StubItem::create()]));

        $this->expectExceptionObject(ItemNotVendedException::becauseItemIsNotFound(StubItemId::createOther()));

        $sut->vendItem(StubItemId::createOther());
    }

    public function test_it_should_fail_when_vending_an_item_but_inserted_money_is_not_enough(): void
    {
        $sut = StubVendingMachine::create(items: ItemCollection::create([StubItem::create()]));

        $this->expectExceptionObject(
            ItemNotVendedException::becauseInsertedMoneyIsNotEnough(StubItem::create(), new FloatValueObject(0))
        );

        $sut->vendItem(StubItemId::create());
    }

    public function test_it_should_fail_when_the_machine_has_no_change(): void
    {
        $sut = StubVendingMachine::create(
            items: ItemCollection::create([StubItem::create()]),
            coins: CoinCollection::create([StubCoin::create()])
        );
        $sut->insertCoin(StubCoin::createRandomWithValue(CoinValue::create(1)));

        $this->expectExceptionObject(ItemNotVendedException::becauseTheMachineHasNoChange(StubItemId::create()));

        $sut->vendItem(StubItemId::create());
    }

    public function test_it_can_collect_vended_item_and_returned_coins(): void
    {
        $item = StubItem::create();
        $sut = StubVendingMachine::create(
            items: ItemCollection::create([$item]),
            coins: CoinCollection::create([StubCoin::createRandomWithValue(CoinValue::create(0.25))])
        );
        $sut->insertCoin(StubCoin::createRandomWithValue(CoinValue::create(1)));

        $sut->vendItem($item->id());

        self::assertEquals($sut->vendedItem(), $item);
        self::assertCount(1, $sut->returnedCoins());

        $sut->collectVendedItemAndReturnedCoins();

        self::assertNull($sut->vendedItem());
        self::assertEmpty($sut->returnedCoins());
        self::assertCount(1, $sut->coins());
    }

    private function thenEventOfClassNameHasBeenRecorded(AggregateRoot $aggregateRoot, string $eventClassName): void
    {
        $this->assertContains(
            $eventClassName,
            $this->classNamesOfRecordedEvents($aggregateRoot),
            "$eventClassName not found between recorded events"
        );
    }

    private function classNamesOfRecordedEvents(AggregateRoot $aggregateRoot): Collection
    {
        return new Collection(
            array_map(static fn(DomainEvent $domainEvent) => $domainEvent::class, $aggregateRoot->pullDomainEvents())
        );
    }
}