<?php

namespace App\Repository;

use App\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\User;

/**
 * @extends ServiceEntityRepository<Transaction>
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function getChartDataByCategory($user, $type)
    {
        return $this->createQueryBuilder('e')
            ->select('e.category, SUM(e.amount) as totalAmount')
            ->andWhere('e.type = :type')
            ->andWhere('e.user = :user')
            ->setParameter('type', $type)
            ->setParameter('user', $user)
            ->groupBy('e.category')
            ->getQuery()
            ->getArrayResult();
    }

    public function search($search, $user, $type)
    {
        return $this->createQueryBuilder('q')
            ->leftJoin('q.account', 'a')
            ->andWhere('q.amount like :search
                    OR q.category like :search
                    OR q.description like :search
                    OR a.name like :search
                    OR q.transaction_date like :search')
            ->andWhere('q.user= :user')
            ->andWhere('q.type= :type')
            ->setParameter('type', $type)
            ->setParameter('search', $search)
            ->setParameter('user', $user);
    }

    public function getTotalAmountByType(string $type, User $user)
    {
        return $this->createQueryBuilder('q')
            ->select('SUM(q.amount) as total_amount')
            ->andWhere('q.type= :type')
            ->andWhere('q.user= :user')
            ->setParameter('type', $type)
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getRecentTransactions(User $user)
    {
        return $this->createQueryBuilder('q')
            ->andWhere('q.user= :user')
            ->orderBy('q.transaction_date', 'DESC')
            ->setMaxResults(6)
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
