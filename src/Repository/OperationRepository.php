<?php

declare(strict_types=1);

/*
 * This file is part of the Compotes package.
 *
 * (c) Alex "Pierstoval" Rock <pierstoval@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Repository;

use App\Entity\Operation;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method null|Operation find($id, $lockMode = null, $lockVersion = null)
 * @method null|Operation findOneBy(array $criteria, array $orderBy = null)
 * @method Operation[]    findAll()
 * @method Operation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OperationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Operation::class);
    }

    public function monthIsPopulated(DateTimeImmutable $month): bool
    {
        $firstDay = DateTimeImmutable::createFromFormat('Y-m-d H:i:s O', \sprintf(
            '%s-%s-1 00:00:00 +000',
            $month->format('Y'),
            $month->format('m')
        ));

        $lastDay = DateTimeImmutable::createFromFormat('Y-m-d H:i:s O', \sprintf(
            '%s-%s-%s 23:59:59 +000',
            $firstDay->format('Y'),
            $firstDay->format('m'),
            $firstDay->format('t')
        ));

        $count = $this->createQueryBuilder('operation')
            ->select('count(operation)')
            ->where('operation.operationDate >= :first_day')
            ->andWhere('operation.operationDate <= :last_day')
            ->setParameter('first_day', $firstDay)
            ->setParameter('last_day', $lastDay)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return $count > 0;
    }

    /**
     * @return Operation[]
     */
    public function findWithTags(): array
    {
        return $this->_em->createQuery(
            <<<DQL
            SELECT operation, tags
            FROM {$this->_entityName} as operation
            LEFT JOIN operation.tags as tags
            DQL
        )
            ->getResult()
        ;
    }
}
