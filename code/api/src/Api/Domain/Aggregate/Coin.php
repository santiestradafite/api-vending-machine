<?php

declare(strict_types=1);

namespace Api\Domain\Aggregate;

use Shared\Domain\Entity;

/**
 * @method CoinId id()
 */
final class Coin extends Entity
{
    private CoinValue $value;

    private function __construct(CoinId $id, CoinValue $value)
    {
        parent::__construct($id);
        $this->value = $value;
    }

    public static function create(CoinId $id, CoinValue $value): self
    {
        return new self($id, $value);
    }

    public function value(): CoinValue
    {
        return $this->value;
    }
}