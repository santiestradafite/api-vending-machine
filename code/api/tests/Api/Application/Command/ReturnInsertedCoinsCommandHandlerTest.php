<?php

declare(strict_types=1);

namespace Api\Application\Command;

use Api\Domain\VendingMachine\Repository\VendingMachineRepositoryInterface;
use Doctrine\ORM\EntityNotFoundException;
use PHPUnit\Framework\TestCase;
use Tests\Api\Infrastructure\Persistence\InMemoryVendingMachineRepository;
use Tests\Api\Infrastructure\VendingMachine\StubCoin;
use Tests\Api\Infrastructure\VendingMachine\StubVendingMachine;
use Tests\Api\Infrastructure\VendingMachine\StubVendingMachineId;

final class ReturnInsertedCoinsCommandHandlerTest extends TestCase
{
    private ReturnInsertedCoinsCommandHandler $sut;
    private VendingMachineRepositoryInterface $vendingMachineRepository;

    protected function setUp(): void
    {
        $this->vendingMachineRepository = new InMemoryVendingMachineRepository();
        $this->sut = new ReturnInsertedCoinsCommandHandler($this->vendingMachineRepository);
    }

    public function test_it_can_return_inserted_coins(): void
    {
        $this->givenAVendingMachineWithInsertedCoins();

        $this->sut->__invoke(new ReturnInsertedCoinCommand(StubVendingMachineId::DEFAULT_ID));

        $vendingMachine = $this->vendingMachineRepository->findOrFail(StubVendingMachineId::create());

        self::assertEmpty($vendingMachine->insertedCoins());
    }

    private function givenAVendingMachineWithInsertedCoins(): void
    {
        $vendingMachine = StubVendingMachine::create();
        $vendingMachine->insertCoin(StubCoin::create());

        $this->vendingMachineRepository->save($vendingMachine);
    }

    public function test_it_fails_when_vending_machine_is_not_found(): void
    {
        $this->expectException(EntityNotFoundException::class);

        $this->sut->__invoke(new ReturnInsertedCoinCommand(StubVendingMachineId::DEFAULT_ID));
    }
}