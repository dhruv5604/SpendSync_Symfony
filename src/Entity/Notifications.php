<?php

namespace App\Entity;

use App\Repository\NotificationsRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: NotificationsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Notifications
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?User $sender = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?User $Receiver = null;

    #[ORM\Column(length: 255)]
    private ?string $message = null;

    #[ORM\Column]
    private ?bool $showToUser = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->Receiver;
    }

    public function setReceiver(?User $Receiver): static
    {
        $this->Receiver = $Receiver;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
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

    public function isShowToUser(): ?bool
    {
        return $this->showToUser;
    }

    public function setShowToUser(bool $showToUser): static
    {
        $this->showToUser = $showToUser;

        return $this;
    }
}
