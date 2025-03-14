<?php

declare(strict_types=1);

namespace Tests\Api\Domain\VendingMachine\Aggregate;

use Api\Domain\VendingMachine\Aggregate\ItemCollection;
use PHPUnit\Framework\TestCase;
use Shared\Domain\StringValueObject;
use Tests\Api\Infrastructure\VendingMachine\StubItem;
use Tests\Api\Infrastructure\VendingMachine\StubItemId;

final class ItemCollectionTest extends TestCase
{
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