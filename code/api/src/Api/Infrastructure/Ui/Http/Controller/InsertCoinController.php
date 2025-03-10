<?php

declare(strict_types=1);

namespace Api\Infrastructure\Ui\Http\Controller;

use Api\Application\Command\InsertCoinCommand;
use Shared\Domain\Command\CommandBus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class InsertCoinController
{
    public function __construct(private readonly CommandBus $commandBus)
    {
    }

    public function __invoke(Request $request, string $vendingMachineId): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->commandBus->dispatch(new InsertCoinCommand($vendingMachineId, $content['coin_value']));

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}