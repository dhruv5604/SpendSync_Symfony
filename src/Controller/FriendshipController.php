<?php

namespace App\Controller;

use App\Entity\Friendships;
use App\Form\FriendshipTypeForm;
use Doctrine\ORM\EntityManagerInterface;
use Dom\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FriendshipController extends AbstractController
{
    #[Route('/friendship', name: 'app_friendship')]
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
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
            'friends' => []
        ]);
    }
}
