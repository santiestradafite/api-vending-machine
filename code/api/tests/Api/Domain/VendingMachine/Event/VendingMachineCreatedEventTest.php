<?php

declare(strict_types=1);

namespace Tests\Api\Domain\VendingMachine\Event;

use Api\Domain\VendingMachine\Event\VendingMachineCreatedEvent;
use PHPUnit\Framework\TestCase;
use Tests\Api\Infrastructure\VendingMachine\StubVendingMachine;

final class VendingMachineCreatedEventTest extends TestCase
{
    public function test_it_can_create_a_vending_machine_created_event(): void
    {
        $vendingMachine = StubVendingMachine::create();

        $sut = VendingMachineCreatedEvent::create(
            $vendingMachine->id(),
            $vendingMachine->name(),
            $vendingMachine->items(),
            $vendingMachine->coins()
        );

        self::assertEquals($vendingMachine->id(), $sut->aggregateId());
        self::assertEquals($vendingMachine->name(), $sut->name());
        self::assertEquals($vendingMachine->items(), $sut->items());
        self::assertEquals($vendingMachine->coins(), $sut->coins());
        self::assertNotNull($sut->eventId());
        self::assertNotNull($sut->occurredOn());
    }
}