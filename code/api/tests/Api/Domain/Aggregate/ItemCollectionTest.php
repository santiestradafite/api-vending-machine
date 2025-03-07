<?php

declare(strict_types=1);

namespace Tests\Api\Domain\Aggregate;

use Api\Domain\Aggregate\ItemCollection;
use PHPUnit\Framework\TestCase;
use Shared\Domain\StringValueObject;
use Tests\Api\Infrastructure\VendingMachine\StubItem;
use Tests\Api\Infrastructure\VendingMachine\StubItemId;

final class ItemCollectionTest extends TestCase
{
    public function test_it_can_filter_by_name(): void
    {
        $sut = ItemCollection::create(
            [StubItem::create(), StubItem::create(StubItemId::createOther(), new StringValueObject('Soda'))]
        );
        $itemsFiltered = $sut->filterByName(new StringValueObject(StubItem::DEFAULT_NAME));

        self::assertCount(1, $itemsFiltered);
        self::assertEquals(StubItemId::create(), $itemsFiltered->firstOrFail()->id());
    }
}