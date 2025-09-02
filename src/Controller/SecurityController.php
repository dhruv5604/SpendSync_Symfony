<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route("/login","app_login")]
    public function login(AuthenticationUtils $authenticationUtils)
    {
        return $this->render('security/login.html.twig',[
            "error" => $authenticationUtils->getLastAuthenticationError(),
            "lastUserName" => $authenticationUtils->getLastUsername()
        ]);
    }
    
    #[Route("/logout","app_logout")]
    public function logout()
    {
        throw new \Exception('This should never be reached');
    }
}