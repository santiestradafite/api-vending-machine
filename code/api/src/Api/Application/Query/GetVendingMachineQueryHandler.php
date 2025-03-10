<?php

declare(strict_types=1);

namespace Api\Application\Query;

use Api\Domain\VendingMachine\Aggregate\VendingMachineId;
use Api\Domain\VendingMachine\Repository\VendingMachineRepositoryInterface;
use Shared\Domain\Query\QueryHandler;

final class GetVendingMachineQueryHandler implements QueryHandler
{
    public function __construct(
        private readonly VendingMachineRepositoryInterface $vendingMachineRepository,
        private readonly GetVendingMachineQueryResponseConverter $responseConverter
    ) {
    }

    public function __invoke(GetVendingMachineQuery $command): GetVendingMachineQueryResponse
    {
        $vendingMachine = $this->vendingMachineRepository->findOrFail(
            VendingMachineId::fromString($command->vendingMachineId())
        );

        return $this->responseConverter->__invoke($vendingMachine);
    }
}