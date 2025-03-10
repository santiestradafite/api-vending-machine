<?php

declare(strict_types=1);

namespace Api\Infrastructure\Persistence\Repository;

use Api\Domain\VendingMachine\Aggregate\VendingMachine;
use Api\Domain\VendingMachine\Aggregate\VendingMachineId;
use Api\Domain\VendingMachine\Repository\VendingMachineRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Shared\Domain\Event\EventBus;
use Shared\Infrastructure\Persistence\Repository\DoctrineEntityRepository;

final class VendingMachineRepository extends DoctrineEntityRepository implements VendingMachineRepositoryInterface
{
    public function __construct(
        EntityManagerInterface $entityManager,
        EventBus $eventBus
    ) {
        parent::__construct($entityManager, $eventBus);
    }

    protected function entityClassName(): string
    {
        return VendingMachine::class;
    }

    public function save(VendingMachine $vendingMachine): void
    {
        $this->saveEntity($vendingMachine);
    }

    public function findOrFail(VendingMachineId $vendingMachineId): VendingMachine
    {
        return $this->doFindOrFail($vendingMachineId);
    }
}
