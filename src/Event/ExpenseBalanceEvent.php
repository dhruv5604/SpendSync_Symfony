<?php

namespace App\Event;

use App\Entity\Account;
use App\Entity\Expense;
use Symfony\Contracts\EventDispatcher\Event;

class ExpenseBalanceEvent extends Event
{
    public const NAME = 'expense.updateBalance';
    private Account $originalAccount;
    private Expense $expense;
    public function __construct(Account $originalAccount, Expense $expense)
    {
        $this->originalAccount = $originalAccount;
        $this->expense = $expense;
    }

    public function getOriginalAccount()
    {
        return $this->originalAccount;
    }

    public function getExpense()
    {
        return $this->expense;
    }
}