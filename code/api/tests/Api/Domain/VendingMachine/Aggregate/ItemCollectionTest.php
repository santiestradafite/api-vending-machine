<?php

declare(strict_types=1);

namespace Api\Domain\VendingMachine\Aggregate;

use PHPUnit\Framework\TestCase;
use Shared\Domain\StringValueObject;
use Tests\Api\Infrastructure\VendingMachine\StubItem;
use Tests\Api\Infrastructure\VendingMachine\StubItemId;

final class ItemCollectionTest extends TestCase
{
    public function test_it_can_filter_by_name(): void
    {
        $item = StubItem::create();
        $otherItem = StubItem::create(StubItemId::createOther(), new StringValueObject('Soda'));
        $sut = ItemCollection::create([$item, $otherItem]);

        $itemsFiltered = $sut->filterByName(new StringValueObject(StubItem::DEFAULT_NAME));

        self::assertCount(1, $itemsFiltered);
        self::assertEquals($item, $itemsFiltered->firstOrFail());
    }

    public function test_it_can_filter_vended_item(): void
    {
        $item = StubItem::create();
        $item->vend();
        $otherItem = StubItem::create(StubItemId::createOther(), new StringValueObject('Soda'));
        $sut = ItemCollection::create([$item, $otherItem]);

        $itemsFiltered = $sut->filterVended();

        self::assertCount(1, $itemsFiltered);
        self::assertEquals($item, $itemsFiltered->firstOrFail());
    }
}