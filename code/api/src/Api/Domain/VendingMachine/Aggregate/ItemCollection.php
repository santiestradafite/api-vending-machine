<?php

declare(strict_types=1);

namespace Api\Domain\VendingMachine\Aggregate;

use Shared\Common\TypedCollection;
use Throwable;

/**
 * @method Item firstOrFail(Throwable $customException = null)
 * @method Item|null get(string $key)
 */
final class ItemCollection extends TypedCollection
{
    protected function type(): string
    {
        return Item::class;
    }

    public function filterVended(): self
    {
        return $this->filter(static fn (Item $item) => $item->isVended()->isTrue());
    }

    public static function indexBy(): callable
    {
        return static fn (Item $item) => $item->id()->value();
    }
}
