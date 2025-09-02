<?php

namespace App\Controller;

use App\Entity\Friendships;
use App\Entity\Notifications;
use App\Form\FriendshipTypeForm;
use App\Repository\FriendshipsRepository;
use App\Repository\NotificationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FriendshipController extends AbstractController
{
    #[Route('/dashboard/friendships', name: 'app_friendship')]
    public function index(Request $request, EntityManagerInterface $entityManager, FriendshipsRepository $friendshipsRepository)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $friends = $friendshipsRepository->findBy(['user1' => $this->getUser()]);

        $friendship = new Friendships();
        $form = $this->createForm(FriendshipTypeForm::class, $friendship, [
            'current_user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $data->setUser1($this->getUser());
            $data->setStatus('pending');

            $notification = new Notifications();
            $notification->setSender($this->getUser());
            $notification->setReceiver($data->getUser2());
            $notification->setMessage('You have a new friendship request from ' . $this->getUser()->getUserIdentifier());

            $entityManager->persist($data);
            $entityManager->persist($notification);

            $entityManager->flush();

            $this->addFlash('success', 'Friendship request sent successfully!');
            return $this->redirectToRoute('app_friendship');
        }

        return $this->render('friendship/index.html.twig', [
            'form' => $form->createView(),
            'friends' => $friends,
            'action' => 'new'
        ]);
    }

    #[Route('/dashboard/friendship/responseToRequest/{id}/{type}/{notificationId}', 'app_response_to_request')]
    public function responseToRequest($type, $id, $notificationId, FriendshipsRepository $friendshipsRepository, NotificationsRepository $notificationsRepository, EntityManagerInterface $entityManager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $friendships = $friendshipsRepository->findBy(['user2' => $id]);
        $notification = $notificationsRepository->find($notificationId);

        foreach ($friendships as $friendship) {
            if ($type == 'accept') {
                $friendship->setStatus('accepted');
            } else {
                $friendship->setStatus('rejected');
            }
            $notification->setShowToUser(false);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_friendship');
    }

    #[Route('/dashboard/friendship/edit/{id}', 'app_edit_friendship')]
    public function editFriend($id, Request $request, EntityManagerInterface $entityManager, FriendshipsRepository $friendshipsRepository)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $friendship = $friendshipsRepository->find($id);

        $friends = $friendshipsRepository->findBy(['user1' => $this->getUser()]);

        if (!$friendship) {
            throw $this->createNotFoundException('No such friendship find!!');
        }

        $form = $this->createForm(FriendshipTypeForm::class, $friendship);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Friendship updated Successfully');
            return $this->redirectToRoute('app_friendship');
        }

        return $this->render('friendship/index.html.twig', [
            'form' => $form->createView(),
            'friends' => $friends,
            'action' => 'edit'
        ]);
    }
}
