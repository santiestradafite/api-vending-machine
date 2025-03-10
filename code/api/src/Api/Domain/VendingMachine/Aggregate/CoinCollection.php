<?php

declare(strict_types=1);

namespace Api\Domain\VendingMachine\Aggregate;

use Shared\Common\TypedCollection;

/**
 * @method CoinCollection sort(callable $criteria)
 */
final class CoinCollection extends TypedCollection
{
    protected function type(): string
    {
        return Coin::class;
    }

    public function sortByValue(): self
    {
        return $this->sort(
            static fn (Coin $coin, Coin $otherCoin) => $otherCoin->value()->value() <=> $coin->value()->value()
        );
    }

    public function filterInserted(): self
    {
        return $this->filter(static fn (Coin $coin) => $coin->isInserted()->isTrue());
    }

    public function filterReturned(): self
    {
        return $this->filter(static fn (Coin $coin) => $coin->isReturned()->isTrue());
    }

    public static function indexBy(): callable
    {
        return static fn (Coin $coin) => $coin->id()->value();
    }
}
