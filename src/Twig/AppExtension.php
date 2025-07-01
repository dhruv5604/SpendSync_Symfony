<?php

namespace App\Twig;

use App\Repository\FriendshipsRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private $friendshipsRepository;
    private $security;

    public function __construct(FriendshipsRepository $friendshipsRepository, Security $security)
    {
        $this->friendshipsRepository = $friendshipsRepository;
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
        return ($this->friendshipsRepository->findPendingRequests($user));
    }
}