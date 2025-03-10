<?php

declare(strict_types=1);

namespace Api\Application\Command;

use Api\Application\Command\SetupVendingMachineCommand;
use Api\Application\Command\SetupVendingMachineCommandHandler;
use Api\Domain\VendingMachine\Exception\ItemNotVendedException;
use Api\Domain\VendingMachine\Repository\VendingMachineRepositoryInterface;
use Doctrine\ORM\EntityNotFoundException;
use PHPUnit\Framework\TestCase;
use Tests\Api\Infrastructure\Persistence\InMemoryVendingMachineRepository;
use Tests\Api\Infrastructure\VendingMachine\StubCoin;
use Tests\Api\Infrastructure\VendingMachine\StubVendingMachine;
use Tests\Api\Infrastructure\VendingMachine\StubVendingMachineId;

final class InsertCoinCommandHandlerTest extends TestCase
{
    private InsertCoinCommandHandler $sut;
    private VendingMachineRepositoryInterface $vendingMachineRepository;

    protected function setUp(): void
    {
        $this->vendingMachineRepository = new InMemoryVendingMachineRepository();
        $this->sut = new InsertCoinCommandHandler($this->vendingMachineRepository);
    }

    public function test_it_can_insert_a_coin(): void
    {
        $this->vendingMachineRepository->save(StubVendingMachine::create());

        $this->sut->__invoke(new InsertCoinCommand(StubVendingMachineId::DEFAULT_ID, StubCoin::DEFAULT_VALUE));

        $vendingMachine = $this->vendingMachineRepository->findOrFail(StubVendingMachineId::create());

        self::assertCount(1, $vendingMachine->insertedCoins());
        self::assertEquals(StubCoin::DEFAULT_VALUE, $vendingMachine->insertedCoins()->first()->value()->value());
    }

    public function test_it_fails_when_vending_machine_is_not_found(): void
    {
        $this->expectException(EntityNotFoundException::class);

        $this->sut->__invoke(new InsertCoinCommand(StubVendingMachineId::DEFAULT_ID, StubCoin::DEFAULT_VALUE));
    }
}