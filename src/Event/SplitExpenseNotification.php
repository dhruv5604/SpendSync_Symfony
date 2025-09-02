<?php

namespace App\Event;

class SplitExpenseNotification
{
    public const NAME = 'split.expense.notification';

    private array $friendsToSplitWith;
    private array $amountsToSplit;

    public function __construct(array $friendsToSplitWith, array $amountsToSplit)
    {
        $this->friendsToSplitWith = $friendsToSplitWith;
        $this->amountsToSplit = $amountsToSplit;
    }

    public function getFriendsToSplitWith(): array
    {
        return $this->friendsToSplitWith;
    }

    public function getAmountsToSplit(): array
    {
        return $this->amountsToSplit;
    }
}