<?php

declare(strict_types=1);

namespace Tests\Api\Domain\VendingMachine\Event;

use Api\Domain\VendingMachine\Event\CoinInsertedEvent;
use PHPUnit\Framework\TestCase;
use Tests\Api\Infrastructure\VendingMachine\StubCoin;
use Tests\Api\Infrastructure\VendingMachine\StubVendingMachine;

final class CoinInsertedEventTest extends TestCase
{
    public function test_it_can_create_a_coin_inserted_event(): void
    {
        $vendingMachine = StubVendingMachine::create();
        $coin = StubCoin::create();
        $vendingMachine->insertCoin($coin);

        $sut = CoinInsertedEvent::create(
            $coin->id(),
            $vendingMachine->id(),
            $coin->value()
        );

        self::assertEquals($coin->id(), $sut->aggregateId());
        self::assertEquals($vendingMachine->id(), $sut->vendingMachineId());
        self::assertEquals($coin->value(), $sut->value());
        self::assertNotNull($sut->eventId());
        self::assertNotNull($sut->occurredOn());
    }
}