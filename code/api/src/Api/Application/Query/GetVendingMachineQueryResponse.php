<?php

declare(strict_types=1);

namespace Api\Application\Query;

use Shared\Domain\Query\Response;

final class GetVendingMachineQueryResponse implements Response
{
    public function __construct(private readonly array $result)
    {
    }

    public function result(): array
    {
        return $this->result;
    }
}
