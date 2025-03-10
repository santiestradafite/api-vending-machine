<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Persistence\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Shared\Domain\AggregateRoot;
use Shared\Domain\Event\EventBus;
use Shared\Domain\Uuid;

abstract class DoctrineEntityRepository
{
    public function __construct(protected EntityManagerInterface $entityManager, protected EventBus $eventBus)
    {
    }

    protected function doFindOrFail(Uuid $id)
    {
        $entity = $this->doFind($id);

        if (null === $entity) {
            throw EntityNotFoundException::forId($id);
        }

        return $entity;
    }

    protected function doFind(Uuid $uuid)
    {
        return $this->getRepository()->find($uuid);
    }

    protected function saveEntity(AggregateRoot $entity): void
    {
        $this->eventBus->publish(...$entity->pullDomainEvents());
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    protected function getRepository(): EntityRepository
    {
        return $this->entityManager->getRepository($this->entityClassName());
    }

    abstract protected function entityClassName(): string;
}
