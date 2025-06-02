<?php

namespace App\EventListener;

use App\Event\IncomeBalanceEvent;
use Doctrine\ORM\EntityManagerInterface;

class IncomeBalanceListener
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onIncomeUpdate(IncomeBalanceEvent $event)
    {
        $income = $event->getTransaction();
        $originalAccount = $event->getOriginalAccount();
        $originalAmount = $event->getOriginalAmount();
        $newAmount = $income->getAmount();
        $newAccount = $income->getAccount();

        $originalAccount->setBalance($originalAccount->getBalance() - $originalAmount);
        $newAccount->setBalance($newAccount->getBalance() + $newAmount);
        
        $this->em->flush();
    }
}
