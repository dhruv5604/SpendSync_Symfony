<?php

namespace App\Event;

use App\Entity\Account;
use App\Entity\Income;
use Symfony\Contracts\EventDispatcher\Event;

class IncomeBalanceEvent extends Event
{
    public const NAME = 'income.updateBalance';
    public function __construct(
        private Account $originalAccount,
        private Income $income, 
        private float $originalAmount
    ) {}

    public function getOriginalAccount()
    {
        return $this->originalAccount;
    }

    public function getIncome()
    {
        return $this->income;
    }

    public function getOriginalAmount() 
    { 
        return $this->originalAmount; 
    }
}