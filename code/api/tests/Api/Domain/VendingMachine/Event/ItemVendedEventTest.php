<?php

declare(strict_types=1);

namespace Tests\Api\Domain\VendingMachine\Event;

use Api\Domain\VendingMachine\Aggregate\CoinValue;
use Api\Domain\VendingMachine\Aggregate\ItemCollection;
use Api\Domain\VendingMachine\Aggregate\ItemPrice;
use Api\Domain\VendingMachine\Event\ItemVendedEvent;
use PHPUnit\Framework\TestCase;
use Tests\Api\Infrastructure\VendingMachine\StubCoin;
use Tests\Api\Infrastructure\VendingMachine\StubItem;
use Tests\Api\Infrastructure\VendingMachine\StubVendingMachine;

final class ItemVendedEventTest extends TestCase
{
    public function test_it_can_create_an_item_vended_event(): void
    {
        $item = StubItem::create(price: ItemPrice::create(1));
        $vendingMachine = StubVendingMachine::create(items: ItemCollection::create([$item]));
        $vendingMachine->insertCoin(StubCoin::createRandomWithValue(CoinValue::create(1)));

        $sut = ItemVendedEvent::create(
            $item->id(),
            $vendingMachine->id(),
            $item->name(),
            $item->price(),
            $item->stock()
        );

        self::assertEquals($item->id(), $sut->aggregateId());
        self::assertEquals($vendingMachine->id(), $sut->vendingMachineId());
        self::assertEquals($item->name(), $sut->name());
        self::assertEquals($item->price(), $sut->price());
        self::assertEquals($item->stock(), $sut->stock());
        self::assertNotNull($sut->eventId());
        self::assertNotNull($sut->occurredOn());
    }
}