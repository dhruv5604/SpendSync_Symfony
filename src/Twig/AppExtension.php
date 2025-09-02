<?php

namespace App\Twig;

use App\Repository\FriendshipsRepository;
use App\Repository\NotificationsRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private $notificationsRepository;
    private $security;

    public function __construct(NotificationsRepository $notificationsRepository, Security $security)
    {
        $this->notificationsRepository = $notificationsRepository;
        $this->security = $security;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_notifications',[$this, 'getNotifications'])
        ];
    }

    public function getNotifications()
    {
        $user = $this->security->getUser();

        if (!$user) {
            return [];
        }

        return ($this->notificationsRepository->findBy(['Receiver' => $user]));
    }
}