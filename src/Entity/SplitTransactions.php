<?php

namespace App\Entity;

use App\Repository\SplitTransactionsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SplitTransactionsRepository::class)]
class SplitTransactions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'splitTransactions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Transaction $parent_transaction = null;

    #[ORM\ManyToOne(inversedBy: 'splitTransactions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?int $amount_owed = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParentTransaction(): ?Transaction
    {
        return $this->parent_transaction;
    }

    public function setParentTransaction(?Transaction $parent_transaction): static
    {
        $this->parent_transaction = $parent_transaction;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAmountOwed(): ?int
    {
        return $this->amount_owed;
    }

    public function setAmountOwed(int $amount_owed): static
    {
        $this->amount_owed = $amount_owed;
        
        return $this;
    }
}
