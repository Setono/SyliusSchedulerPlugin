<?php

declare(strict_types=1);

namespace Setono\SyliusSchedulerPlugin\Doctrine\ORM;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class ScheduleRepository extends EntityRepository implements ScheduleRepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createOrderedQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('s')
            ->addOrderBy('s.priority', 'DESC')
            ->addOrderBy('s.id', 'ASC')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findByQueues(array $restrictedQueues = []): array
    {
        $queryBuilder = $this->createOrderedQueryBuilder();

        if (!empty($restrictedQueues)) {
            $queryBuilder
                ->andWhere('s.queue IN :restrictedQueues')
                ->setParameter('restrictedQueues', $restrictedQueues, Connection::PARAM_STR_ARRAY)
            ;
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
