<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    Private $idUsercon=1;

    #[Route('/cart', name: 'app_cart')]
    public function index(): Response
    {
        $entityManager=$this->getDoctrine()->getManager();
        $panier = $entityManager->getRepository(Panier::class)->findBy([
            'idUser' => $idUsercon
        ]);
        return $this->render('cart.html.twig', [
            'controller_name' => 'CartController',
        ]);
    }
}
