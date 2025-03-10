<?php

declare(strict_types=1);

namespace Tests\Api\Application\Command;

use Api\Application\Command\SetupVendingMachineCommand;
use Api\Application\Command\SetupVendingMachineCommandHandler;
use Api\Domain\VendingMachine\Repository\VendingMachineRepositoryInterface;
use PHPUnit\Framework\TestCase;
use Tests\Api\Infrastructure\Persistence\InMemoryVendingMachineRepository;
use Tests\Api\Infrastructure\VendingMachine\StubVendingMachineId;

final class SetupVendingMachineCommandHandlerTest extends TestCase
{
    private SetupVendingMachineCommandHandler $sut;
    private VendingMachineRepositoryInterface $vendingMachineRepository;

    protected function setUp(): void
    {
        $this->vendingMachineRepository = new InMemoryVendingMachineRepository();
        $this->sut = new SetupVendingMachineCommandHandler($this->vendingMachineRepository);
    }

    public function test_it_sets_up_vending_machine_with_expected_items_and_coins(): void
    {
        $this->sut->__invoke(new SetupVendingMachineCommand(StubVendingMachineId::DEFAULT_ID));

        $vendingMachine = $this->vendingMachineRepository->findOrFail(StubVendingMachineId::create());

        self::assertEquals(StubVendingMachineId::create(), $vendingMachine->id());
        self::assertCount(3, $vendingMachine->items());
        self::assertCount(4, $vendingMachine->coins());
        self::assertEmpty($vendingMachine->insertedCoins());
        self::assertEmpty($vendingMachine->returnedCoins());
        self::assertNull($vendingMachine->vendedItem());
    }
}