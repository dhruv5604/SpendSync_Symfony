<?php

namespace App\Event;

use App\Entity\Account;
use App\Entity\Income;
use App\Entity\Transaction;
use Symfony\Contracts\EventDispatcher\Event;

class IncomeBalanceEvent extends Event
{
    public const NAME = 'income.updateBalance';
    public function __construct(
        private Account $originalAccount,
        private Transaction $transaction, 
        private float $originalAmount
    ) {}

    public function getTransaction() 
    { 
        return $this->transaction; 
    }

    public function getOriginalAmount() 
    { 
        return $this->originalAmount; 
    }

    public function getOriginalAccount() 
    { 
        return $this->originalAccount; 
    }
}