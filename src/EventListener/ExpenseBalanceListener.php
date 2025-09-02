<?php

namespace App\EventListener;

use App\Event\ExpenseBalanceEvent;
use Doctrine\ORM\EntityManagerInterface;

class ExpenseBalanceListener
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onExpenseUpdate(ExpenseBalanceEvent $event)
    {
        $expense = $event->getTransaction();
        $originalAccount = $event->getOriginalAccount();

        $amount = $expense->getAmount();

        $originalAccount->setBalance($originalAccount->getBalance() - $amount);
        $expense->getAccount()->setBalance($expense->getAccount()->getBalance() + $amount);
        $this->em->flush();
    }
}
