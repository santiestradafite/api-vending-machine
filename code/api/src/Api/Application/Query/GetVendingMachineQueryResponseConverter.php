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
                    'name' => $vendingMachine->name()->value(),
                    'vended_item' => $this->convertVendedItem($vendingMachine->vendedItem()),
                    'returned_coins' => $vendingMachine->returnedCoins()->reduce($this->convertReturnedCoin(), []),
                    'items' => $vendingMachine->items()->reduce($this->convertItem(), []),
                    'coins' => $vendingMachine->coins()->reduce($this->convertCoin(), [])
                ]
            ]
        );
    }

    private function convertVendedItem(?Item $item): ?array
    {
        if (null === $item) {
            return null;
        }

        return [
            'id' => $item->id()->value(),
            'name' => $item->name()->value(),
            'price' => $item->price()->value(),
            'stock' => $item->stock()->value()
        ];
    }

    private function convertReturnedCoin(): Closure
    {
        return static function (array $carry, Coin $coin) {
            $carry[] = [
                'id' => $coin->id()->value(),
                'value' => $coin->value()->value()
            ];

            return $carry;
        };
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
