<?php

declare(strict_types=1);

namespace Tests\Api\Domain\Aggregate;

use Api\Domain\Aggregate\CoinCollection;
use Api\Domain\Aggregate\CoinValue;
use Api\Domain\Aggregate\ItemCollection;
use Api\Domain\Aggregate\ItemPrice;
use Api\Domain\Aggregate\VendingMachine;
use Api\Domain\Event\VendingMachineCreatedEvent;
use Api\Domain\Exception\ItemNotVendedException;
use PHPUnit\Framework\TestCase;
use Shared\Domain\FloatValueObject;
use Shared\Domain\IntValueObject;
use Shared\Domain\StringValueObject;
use Tests\Api\Infrastructure\VendingMachine\StubCoin;
use Tests\Api\Infrastructure\VendingMachine\StubItem;
use Tests\Api\Infrastructure\VendingMachine\StubVendingMachine;
use Tests\Api\Infrastructure\VendingMachine\StubVendingMachineId;

final class VendingMachineTest extends TestCase
{
    public function test_it_can_create_a_vending_machine(): void
    {
        $id = StubVendingMachineId::create();
        $items = ItemCollection::create([StubItem::create()]);
        $coins = CoinCollection::create([StubCoin::create()]);

        $sut = VendingMachine::create($id, $items, $coins);

        self::assertEquals($id, $sut->id());
        self::assertEquals($items, $sut->items());
        self::assertEquals($coins, $sut->availableCoins());
        self::assertEmpty($sut->insertedCoins());
        self::assertEmpty($sut->returnedCoins());
        self::assertNull($sut->vendedItem());

        $events = $sut->pullDomainEvents();
        self::assertCount(1, $events);
        /** @var VendingMachineCreatedEvent $event */
        $event = array_shift($events);
        self::assertEquals(VendingMachineCreatedEvent::class, get_class($event));
        self::assertEquals(StubVendingMachineId::DEFAULT_ID, $event->aggregateId());
        self::assertEquals($items, $event->items());
        self::assertEquals($coins, $event->availableCoins());
    }

    public function test_it_can_add_a_new_item(): void
    {
        $sut = StubVendingMachine::create();
        $name = new StringValueObject(StubItem::DEFAULT_NAME);
        $price = ItemPrice::create(StubItem::DEFAULT_PRICE);
        $stock = new IntValueObject(StubItem::DEFAULT_STOCK);

        $sut->addItem($name, $price, $stock);

        self::assertCount(1, $sut->items());
        $itemAdded = $sut->items()->firstOrFail();
        self::assertEquals($name, $itemAdded->name());
        self::assertEquals($price, $itemAdded->price());
        self::assertEquals($stock, $itemAdded->stock());
    }

    public function test_it_can_update_an_existing_item(): void
    {
        $sut = StubVendingMachine::create(items: ItemCollection::create([StubItem::create()]));
        $name = new StringValueObject(StubItem::DEFAULT_NAME);
        $price = ItemPrice::create(0.25);
        $stock = new IntValueObject(1);

        $sut->addItem($name, $price, $stock);

        self::assertCount(1, $sut->items());
        $itemAdded = $sut->items()->firstOrFail();
        self::assertEquals($name, $itemAdded->name());
        self::assertEquals($price, $itemAdded->price());
        self::assertEquals($stock, $itemAdded->stock());
    }

    public function test_it_can_insert_a_coin(): void
    {
        $sut = StubVendingMachine::create(availableCoins: CoinCollection::create([StubCoin::create()]));
        $sut->insertCoin(StubCoin::createRandomWithValue(CoinValue::create(0.25)));

        self::assertCount(1, $sut->insertedCoins());
        self::assertCount(2, $sut->availableCoins());
        self::assertEmpty($sut->returnedCoins());
    }

    public function test_it_can_get_inserted_money(): void
    {
        $sut = StubVendingMachine::create();
        $sut->insertCoin(StubCoin::create());
        $sut->insertCoin(StubCoin::createRandomWithValue(CoinValue::create(0.25)));

        self::assertEquals(new FloatValueObject(0.30), $sut->insertedMoney());
    }

    public function test_it_can_return_inserted_coins(): void
    {
        $sut = StubVendingMachine::create(availableCoins: CoinCollection::create([StubCoin::create()]));
        $sut->insertCoin(StubCoin::createRandomWithValue(CoinValue::create(0.25)));
        $sut->insertCoin(StubCoin::createRandomWithValue(CoinValue::create(0.05)));

        $sut->returnInsertedCoins();

        self::assertCount(2, $sut->returnedCoins());
        self::assertEmpty($sut->insertedCoins());
        self::assertCount(1, $sut->availableCoins());
    }

    public function test_it_can_vend_an_item_with_exact_price(): void
    {
        $item = StubItem::create();
        $sut = StubVendingMachine::create(
            items: ItemCollection::create([$item]),
            availableCoins: CoinCollection::create([StubCoin::create()])
        );
        $sut->insertCoin(StubCoin::createRandomWithValue(CoinValue::create(0.25)));
        $sut->insertCoin(StubCoin::createRandomWithValue(CoinValue::create(0.25)));
        $sut->insertCoin(StubCoin::createRandomWithValue(CoinValue::create(0.25)));

        $sut->vendItem($item->name());

        self::assertEquals($sut->vendedItem(), $item);
        self::assertEquals(new IntValueObject(2), $item->stock());
        self::assertEmpty($sut->insertedCoins());
        self::assertEmpty($sut->returnedCoins());
        self::assertCount(4, $sut->availableCoins());
    }

    public function test_it_can_vend_an_item_and_return_change(): void
    {
        $availableCoin = StubCoin::create();
        $item = StubItem::create(price: ItemPrice::create(0.10));
        $sut = StubVendingMachine::create(
            items: ItemCollection::create([$item]),
            availableCoins: CoinCollection::create([$availableCoin])
        );
        $insertedCoin1 = StubCoin::createRandomWithValue(CoinValue::create(1));
        $insertedCoin2 = StubCoin::createRandomWithValue(CoinValue::create(0.25));
        $insertedCoin3 = StubCoin::createRandomWithValue(CoinValue::create(0.10));
        $insertedCoin4 = StubCoin::createRandomWithValue(CoinValue::create(0.05));
        $sut->insertCoin($insertedCoin1);
        $sut->insertCoin($insertedCoin2);
        $sut->insertCoin($insertedCoin3);
        $sut->insertCoin($insertedCoin4);

        $sut->vendItem($item->name());

        self::assertEquals($sut->vendedItem(), $item);
        self::assertEquals(new IntValueObject(2), $item->stock());
        self::assertEmpty($sut->insertedCoins());
        self::assertCount(3, $sut->returnedCoins());
        self::assertContains($insertedCoin1, $sut->returnedCoins());
        self::assertContains($insertedCoin2, $sut->returnedCoins());
        self::assertContains($availableCoin, $sut->returnedCoins());
        self::assertCount(2, $sut->availableCoins());
        self::assertContains($insertedCoin3, $sut->availableCoins());
        self::assertContains($insertedCoin4, $sut->availableCoins());
    }

    public function test_it_should_fail_when_item_is_not_found(): void
    {
        $sut = StubVendingMachine::create(items: ItemCollection::create([StubItem::create()]));
        $itemName = new StringValueObject('Soda');

        $this->expectExceptionObject(ItemNotVendedException::becauseItemIsNotFound($itemName));
        $sut->vendItem($itemName);
    }

    public function test_it_should_fail_when_item_has_no_stock(): void
    {
        $sut = StubVendingMachine::create(items: ItemCollection::create([StubItem::create(stock: new IntValueObject(0))]));
        $itemName = new StringValueObject(StubItem::DEFAULT_NAME);

        $this->expectExceptionObject(ItemNotVendedException::becauseItemHasNoStock($itemName));
        $sut->vendItem($itemName);
    }

    public function test_it_should_fail_when_inserted_money_is_not_enough(): void
    {
        $sut = StubVendingMachine::create(items: ItemCollection::create([StubItem::create()]));
        $itemName = new StringValueObject(StubItem::DEFAULT_NAME);

        $this->expectExceptionObject(ItemNotVendedException::becauseInsertedMoneyIsNotEnough($itemName));
        $sut->vendItem($itemName);
    }

    public function test_it_can_clear_vended_item_and_change(): void
    {
        $item = StubItem::create();
        $sut = StubVendingMachine::create(
            items: ItemCollection::create([$item]),
            availableCoins: CoinCollection::create([StubCoin::createRandomWithValue(CoinValue::create(0.25))])
        );
        $sut->insertCoin(StubCoin::createRandomWithValue(CoinValue::create(1)));

        $sut->vendItem($item->name());

        self::assertEquals($sut->vendedItem(), $item);
        self::assertCount(1, $sut->returnedCoins());

        $sut->clearVendedItemAndChange();

        self::assertNull($sut->vendedItem());
        self::assertEmpty($sut->returnedCoins());
    }
}