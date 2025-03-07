<?php

declare(strict_types=1);

namespace Api\Domain\Aggregate;

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
}
