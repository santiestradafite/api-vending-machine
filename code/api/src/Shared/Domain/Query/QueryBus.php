<?php

declare(strict_types=1);

namespace Shared\Domain\Query;

interface QueryBus
{
    public function ask(Query $query): Response;
}
