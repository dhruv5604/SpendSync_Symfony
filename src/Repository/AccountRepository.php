<?php

namespace App\Repository;

use App\Entity\Account;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Account>
 */
class AccountRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Account::class);
    }

    public function getAccountChartDataByCategory($user)
    {
        return $this->createQueryBuilder('e')
            ->select('e.name, e.balance as totalAmount')
            ->where('e.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getArrayResult();
    }

    public function getTotalAccountBalance(User $user)
    {
        return $this->createQueryBuilder('q')
            ->select('SUM(q.balance) as totalAmount')
            ->andWhere('q.user= :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
