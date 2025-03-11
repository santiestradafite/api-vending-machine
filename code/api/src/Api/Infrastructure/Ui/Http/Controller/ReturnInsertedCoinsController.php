<?php

declare(strict_types=1);

namespace Api\Infrastructure\Ui\Http\Controller;

use Api\Application\Command\ReturnInsertedCoinsCommand;
use Shared\Domain\Command\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class ReturnInsertedCoinsController
{
    public function __construct(private readonly CommandBus $commandBus)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $vendingMachineId = $request->get('vendingMachineId');

        $this->commandBus->dispatch(new ReturnInsertedCoinsCommand($vendingMachineId));

        return new JsonResponse([], Response::HTTP_OK);
    }
}