<?php

namespace App\Repository;

use App\Entity\Expense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Expense>
 */
class ExpenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Expense::class);
    }

    public function search($search, $user)
    {
        return $this->createQueryBuilder('q')
                ->andWhere('q.Amount like :search 
                    OR q.Category like :search
                    OR q.description like :search
                    OR q.expenseDate like :search')
                ->andWhere('q.user= :user')
                ->setParameter('search', $search)
                ->setParameter('user', $user);
    }

    public function getExpenseChartDataByCategory($user)
    {
        return $this->createQueryBuilder('e')
            ->select('e.Category, SUM(e.Amount) as totalAmount')
            ->where('e.user = :user')
            ->setParameter('user', $user)
            ->groupBy('e.Category')
            ->getQuery()
            ->getArrayResult();
    }
}
