<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DemoController extends AbstractController
{
    #[Route("/","app_homepage")]
    public function homepage()
    {
        return $this->render("homepage.html.twig");
    }
}