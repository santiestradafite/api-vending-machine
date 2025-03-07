<?php

declare(strict_types=1);

namespace Tests\Api\Domain\Aggregate;

use Api\Domain\Aggregate\Item;
use Api\Domain\Aggregate\ItemPrice;
use PHPUnit\Framework\TestCase;
use Shared\Domain\IntValueObject;
use Shared\Domain\StringValueObject;
use Tests\Api\Infrastructure\VendingMachine\StubItem;
use Tests\Api\Infrastructure\VendingMachine\StubItemId;

final class ItemTest extends TestCase
{
    public function test_it_can_create_an_item(): void
    {
        $id = StubItemId::create();
        $name = new StringValueObject(StubItem::DEFAULT_NAME);
        $price = ItemPrice::create(StubItem::DEFAULT_PRICE);
        $stock = new IntValueObject(StubItem::DEFAULT_STOCK);

        $sut = Item::create($id, $name, $price, $stock);

        self::assertEquals($id, $sut->id());
        self::assertEquals($name, $sut->name());
        self::assertEquals($price, $sut->price());
        self::assertEquals($stock, $sut->stock());
    }

    public function test_it_can_update_an_item_price_and_stock(): void
    {
        $newPrice = ItemPrice::create(0.10);
        $newStock = new IntValueObject(5);

        $sut = StubItem::create();
        $sut->update($newPrice, $newStock);

        self::assertEquals($newPrice, $sut->price());
        self::assertEquals($newStock, $sut->stock());
    }

    public function test_it_can_reduce_an_item_stock(): void
    {
        $sut = StubItem::create();
        $sut->reduceStock();

        self::assertEquals(new IntValueObject(2), $sut->stock());
    }
}