<?php

namespace Api\Infrastructure\Ui\Http\Controller;

use Api\Application\Command\CollectItemAndCoinsCommand;
use Api\Application\Query\GetVendingMachineQuery;
use Shared\Domain\Command\CommandBus;
use Shared\Domain\Query\QueryBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class GetVendingMachineController
{
    public function __construct(private readonly QueryBus $queryBus, private readonly CommandBus $commandBus)
    {
    }

    public function __invoke(Request $request, string $vendingMachineId): JsonResponse
    {
        $vendingMachine = $this->queryBus->ask(new GetVendingMachineQuery($vendingMachineId));

        $this->commandBus->dispatch(new CollectItemAndCoinsCommand($vendingMachineId));

        return new JsonResponse($vendingMachine->result());
    }
}