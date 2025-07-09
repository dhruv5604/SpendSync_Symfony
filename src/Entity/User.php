<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableEntity;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;
    
    #[ORM\Column(type: 'json')]
    private $roles = [];

    private $plainPassword;

    /**
     * @var Collection<int, Account>
     */
    #[ORM\OneToMany(targetEntity: Account::class, mappedBy: 'user')]
    private Collection $accounts;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'user_id')]
    private Collection $transactions;

    /**
     * @var Collection<int, Friendships>
     */
    #[ORM\OneToMany(targetEntity: Friendships::class, mappedBy: 'user1')]
    private Collection $friendships;

    /**
     * @var Collection<int, SplitTransactions>
     */
    #[ORM\OneToMany(targetEntity: SplitTransactions::class, mappedBy: 'user')]
    private Collection $splitTransactions;

    /**
     * @var Collection<int, Notifications>
     */
    #[ORM\OneToMany(targetEntity: Notifications::class, mappedBy: 'sender')]
    private Collection $notifications;

    public function __construct()
    {
        $this->accounts = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->friendships = new ArrayCollection();
        $this->splitTransactions = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * Set the value of plainPassword
     *
     * @return  self
     */ 
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials(): void
    {

    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return Collection<int, Account>
     */
    public function getAccounts(): Collection
    {
        return $this->accounts;
    }

    public function addAccount(Account $account): static
    {
        if (!$this->accounts->contains($account)) {
            $this->accounts->add($account);
            $account->setUser($this);
        }

        return $this;
    }

    public function removeAccount(Account $account): static
    {
        if ($this->accounts->removeElement($account)) {
            // set the owning side to null (unless already changed)
            if ($account->getUser() === $this) {
                $account->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setUser($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getUser() === $this) {
                $transaction->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Friendships>
     */
    public function getFriendships(): Collection
    {
        return $this->friendships;
    }

    public function addFriendship(Friendships $friendship): static
    {
        if (!$this->friendships->contains($friendship)) {
            $this->friendships->add($friendship);
            $friendship->setUser1($this);
        }

        return $this;
    }

    public function removeFriendship(Friendships $friendship): static
    {
        if ($this->friendships->removeElement($friendship)) {
            // set the owning side to null (unless already changed)
            if ($friendship->getUser1() === $this) {
                $friendship->setUser1(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SplitTransactions>
     */
    public function getSplitTransactions(): Collection
    {
        return $this->splitTransactions;
    }

    public function addSplitTransaction(SplitTransactions $splitTransaction): static
    {
        if (!$this->splitTransactions->contains($splitTransaction)) {
            $this->splitTransactions->add($splitTransaction);
            $splitTransaction->setUser($this);
        }

        return $this;
    }

    public function removeSplitTransaction(SplitTransactions $splitTransaction): static
    {
        if ($this->splitTransactions->removeElement($splitTransaction)) {
            // set the owning side to null (unless already changed)
            if ($splitTransaction->getUser() === $this) {
                $splitTransaction->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notifications>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notifications $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setSender($this);
        }

        return $this;
    }

    public function removeNotification(Notifications $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getSender() === $this) {
                $notification->setSender(null);
            }
        }

        return $this;
    }

}
