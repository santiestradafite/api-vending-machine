<?php

declare(strict_types=1);

namespace Api\Application\Query;

use Api\Domain\VendingMachine\Aggregate\Coin;
use Api\Domain\VendingMachine\Aggregate\Item;
use Api\Domain\VendingMachine\Aggregate\VendingMachine;
use Closure;

final class GetVendingMachineQueryResponseConverter
{
    public function __invoke(VendingMachine $vendingMachine): GetVendingMachineQueryResponse
    {
        return new GetVendingMachineQueryResponse(
            [
                'vendingMachine' => [
                    'id' => $vendingMachine->id()->value(),
                    'items' => $vendingMachine->items()->reduce($this->convertItem(), []),
                    'coins' => $vendingMachine->coins()->reduce($this->convertCoin(), [])
                ]
            ]
        );
    }

    private function convertItem(): Closure
    {
        return static function (array $carry, Item $item) {
            $carry[] = [
                'id' => $item->id()->value(),
                'name' => $item->name()->value(),
                'price' => $item->price()->value(),
                'stock' => $item->stock()->value(),
                'is_vended' => $item->isVended()->value()
            ];

            return $carry;
        };
    }

    private function convertCoin(): Closure
    {
        return static function (array $carry, Coin $coin) {
            $carry[] = [
                'id' => $coin->id()->value(),
                'value' => $coin->value()->value(),
                'is_inserted' => $coin->isInserted()->value(),
                'is_returned' => $coin->isReturned()->value()
            ];

            return $carry;
        };
    }
}
