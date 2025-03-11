<?php

declare(strict_types=1);

namespace Tests\Api\Application\Query;

use Api\Application\Query\GetVendingMachineQuery;
use Api\Application\Query\GetVendingMachineQueryHandler;
use Api\Application\Query\GetVendingMachineQueryResponseConverter;
use Api\Domain\VendingMachine\Aggregate\CoinCollection;
use Api\Domain\VendingMachine\Aggregate\CoinValue;
use Api\Domain\VendingMachine\Aggregate\ItemCollection;
use Api\Domain\VendingMachine\Aggregate\ItemPrice;
use Api\Domain\VendingMachine\Repository\VendingMachineRepositoryInterface;
use Doctrine\ORM\EntityNotFoundException;
use PHPUnit\Framework\TestCase;
use Shared\Domain\IntValueObject;
use Shared\Domain\StringValueObject;
use Tests\Api\Infrastructure\Persistence\InMemoryVendingMachineRepository;
use Tests\Api\Infrastructure\VendingMachine\StubCoin;
use Tests\Api\Infrastructure\VendingMachine\StubCoinId;
use Tests\Api\Infrastructure\VendingMachine\StubItem;
use Tests\Api\Infrastructure\VendingMachine\StubItemId;
use Tests\Api\Infrastructure\VendingMachine\StubVendingMachine;
use Tests\Api\Infrastructure\VendingMachine\StubVendingMachineId;

final class GetVendingMachineQueryHandlerTest extends TestCase
{
    private GetVendingMachineQueryHandler $sut;
    private VendingMachineRepositoryInterface $vendingMachineRepository;

    protected function setUp(): void
    {
        $this->vendingMachineRepository = new InMemoryVendingMachineRepository();
        $this->sut = new GetVendingMachineQueryHandler(
            $this->vendingMachineRepository,
            new GetVendingMachineQueryResponseConverter()
        );
    }

    public function test_it_returns_expected_vending_machine_data(): void
    {
        $this->givenAVendingMachine(true);

        $result = $this->sut->__invoke(new GetVendingMachineQuery(StubVendingMachineId::DEFAULT_ID));

        self::assertEquals(
            [
                'vendingMachine' => [
                    'id' => StubVendingMachineId::DEFAULT_ID,
                    'name' => StubVendingMachine::DEFAULT_NAME,
                    'vended_item' => [
                        'id' => StubItemId::DEFAULT_ID,
                        'name' => StubItem::DEFAULT_NAME,
                        'price' => 0.05,
                        'stock' => 9
                    ],
                    'returned_coins' => [
                        [
                            'id' => StubCoinId::DEFAULT_ID,
                            'value' => 0.05
                        ]
                    ],
                    'items' => [
                        [
                            'id' => StubItemId::DEFAULT_ID,
                            'name' => StubItem::DEFAULT_NAME,
                            'price' => 0.05,
                            'stock' => 9,
                            'is_vended' => true
                        ],
                        [
                            'id' => StubItemId::OTHER_ID,
                            'name' => 'Juice',
                            'price' => 1,
                            'stock' => 5,
                            'is_vended' => false
                        ]
                    ],
                    'coins' => [
                        [
                            'id' => StubCoinId::DEFAULT_ID,
                            'value' => 0.05,
                            'is_inserted' => false,
                            'is_returned' => true
                        ],
                        [
                            'id' => StubCoinId::OTHER_ID,
                            'value' => 0.10,
                            'is_inserted' => false,
                            'is_returned' => false
                        ]
                    ]
                ]
            ],
            $result->result()
        );
    }

    private function givenAVendingMachine(bool $withVendedItemAndReturnedCoins): void
    {
        $items = ItemCollection::create([
            StubItem::create(
                StubItemId::create(),
                new StringValueObject(StubItem::DEFAULT_NAME),
                ItemPrice::create(0.05),
                new IntValueObject(10)
            ),
            StubItem::create(
                StubItemId::createOther(),
                new StringValueObject('Juice'),
                ItemPrice::create(1),
                new IntValueObject(5)
            ),
        ]);

        $coins = CoinCollection::create([StubCoin::create(value: CoinValue::create(0.05))]);

        $vendingMachine = StubVendingMachine::create(items: $items, coins: $coins);

        if ($withVendedItemAndReturnedCoins) {
            $vendingMachine->insertCoin(StubCoin::create(StubCoinId::createOther(), CoinValue::create(0.10)));
            $vendingMachine->vendItem(StubItemId::create());
        }

        $this->vendingMachineRepository->save($vendingMachine);
    }

    public function test_it_returns_expected_vending_machine_data_without_vended_item_and_returned_coins(): void
    {
        $this->givenAVendingMachine(false);

        $result = $this->sut->__invoke(new GetVendingMachineQuery(StubVendingMachineId::DEFAULT_ID));

        self::assertEquals(
            [
                'vendingMachine' => [
                    'id' => StubVendingMachineId::DEFAULT_ID,
                    'name' => StubVendingMachine::DEFAULT_NAME,
                    'vended_item' => null,
                    'returned_coins' => [],
                    'items' => [
                        [
                            'id' => StubItemId::DEFAULT_ID,
                            'name' => StubItem::DEFAULT_NAME,
                            'price' => 0.05,
                            'stock' => 10,
                            'is_vended' => false
                        ],
                        [
                            'id' => StubItemId::OTHER_ID,
                            'name' => 'Juice',
                            'price' => 1,
                            'stock' => 5,
                            'is_vended' => false
                        ]
                    ],
                    'coins' => [
                        [
                            'id' => StubCoinId::DEFAULT_ID,
                            'value' => 0.05,
                            'is_inserted' => false,
                            'is_returned' => false
                        ]
                    ]
                ]
            ],
            $result->result()
        );
    }

    public function test_it_fails_when_vending_machine_is_not_found(): void
    {
        $this->expectException(EntityNotFoundException::class);

        $this->sut->__invoke(new GetVendingMachineQuery(StubVendingMachineId::DEFAULT_ID));
    }
}