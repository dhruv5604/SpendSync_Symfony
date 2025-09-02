<?php

namespace App\EventListener;

use App\Entity\Notifications;
use App\Event\SplitExpenseNotification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class SplitExpenseNotificationListener
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function onSplitExpenseNotification(SplitExpenseNotification $event)
    {
        $friendsToSplitWith = $event->getFriendsToSplitWith();
        $amountsToSplit = $event->getAmountsToSplit();

        foreach ($friendsToSplitWith as $index => $friend) {

            $notification = new Notifications();
            $notification->setSender($this->security->getUser());
            $notification->setReceiver($friend->getUser2());
            $notification->setMessage(sprintf('You have been added to a split expense of %s', $amountsToSplit[$index]));

            $this->em->persist($notification);
        }

        $this->em->flush();
    }
}
