<?php

declare(strict_types=1);

namespace Api\Infrastructure\Ui\Http\Controller;

use Api\Application\Command\SetupVendingMachineCommand;
use Shared\Domain\Command\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SetupVendingMachineController
{
    public function __construct(private readonly CommandBus $commandBus)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $vendingMachineId = $request->get('vendingMachineId');

        $this->commandBus->dispatch(new SetupVendingMachineCommand($vendingMachineId));

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}