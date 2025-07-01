<?php

namespace App\Controller;

use App\Entity\Friendships;
use App\Form\FriendshipTypeForm;
use App\Repository\FriendshipsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FriendshipController extends AbstractController
{
    #[Route('/friendship', name: 'app_friendship')]
    public function index(Request $request, EntityManagerInterface $entityManager, FriendshipsRepository $friendshipsRepository)
    {   

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

            $entityManager->persist($data);
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

    #[Route('/friendship/responseToRequest/{id}/{type}', 'app_response_to_request')]
    public function responseToRequest($type, $id, FriendshipsRepository $friendshipsRepository, EntityManagerInterface $entityManager) 
    {
        $notification = $friendshipsRepository->find($id);

        if ($type == 'accept') {
            $notification->setStatus('accepted');
        } else   {
            $notification->setStatus('rejected');
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_friendship');
    }

    #[Route('/friendship/edit/{id}', 'app_edit_friendship')]
    public function editFriend($id, Request $request, EntityManagerInterface $entityManager, FriendshipsRepository $friendshipsRepository)
    {
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
