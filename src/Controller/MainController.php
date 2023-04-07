<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/base', name: 'app_base')]
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/main', name: 'app_main')]
    public function indexMain(): Response
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function indexLogin(): Response
    {
        return $this->render('main/login.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function indexRegister(): Response
    {
        return $this->render('main/register.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
