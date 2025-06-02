<?php

namespace App\Event;

use App\Entity\Account;
use App\Entity\Expense;
use App\Entity\Transaction;
use Symfony\Contracts\EventDispatcher\Event;

class ExpenseBalanceEvent extends Event
{
    public const NAME = 'expense.updateBalance';
    private Account $originalAccount;
    private Transaction $transaction;
    public function __construct(Account $originalAccount, Transaction $transaction)
    {
        $this->originalAccount = $originalAccount;
        $this->transaction = $transaction;
    }

    public function getOriginalAccount()
    {
        return $this->originalAccount;
    }

    public function getTransaction()
    {
        return $this->transaction;
    }
}