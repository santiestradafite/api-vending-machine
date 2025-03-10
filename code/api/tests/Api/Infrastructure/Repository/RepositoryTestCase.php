<?php

declare(strict_types=1);

namespace Tests\Api\Infrastructure\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Shared\Infrastructure\Persistence\Repository\DoctrineEntityRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

abstract class RepositoryTestCase extends KernelTestCase
{
    protected EntityManagerInterface $em;

    /** @var EntityRepository */
    protected $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->arrange();
        $this->em = $this->getEntityManager();
        $this->repository = $this->repository();

        parent::setUp();
    }

    protected function purgeTables(string ...$tables): void
    {
        $conn = $this->getEntityManager()->getConnection();

        $this->executeWithoutForeignKeyChecks($conn, function () use ($tables, $conn): void {
            foreach ($tables as $tableName) {
                $truncateSql = $conn->getDatabasePlatform()->getTruncateTableSQL($tableName);
                $conn->executeStatement($truncateSql);
            }
        });
    }

    protected function executeWithoutForeignKeyChecks(Connection $conn, callable $callable): void
    {
        $conn->executeStatement('SET FOREIGN_KEY_CHECKS = 0;');
        $callable();
        $conn->executeStatement('SET FOREIGN_KEY_CHECKS = 1;');
    }

    abstract protected function arrange(): void;

    abstract protected function repository(): DoctrineEntityRepository;

    private function getEntityManager(): EntityManagerInterface
    {
        /** @var ManagerRegistry $managerRegistry */
        $managerRegistry = static::getContainer()->get('test.' . ManagerRegistry::class);
        $em = $managerRegistry->getManager('default');

        /** @var EntityManagerInterface $em */
        return $em;
    }
}
