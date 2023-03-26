<?php

namespace App\Controller;
use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
  

    #[Route('/cart', name: 'app_cart')]
    public function index(): Response
    {
        $idUsercon=15;
        $entityManager=$this->getDoctrine()->getManager();
        $panier = $entityManager->getRepository(Panier::class)->findBy([
            'idUser' => $idUsercon
        ]);
        $totalPrice = 0;

        foreach ($panier as $item) {
            $product = $item->getIdProduit();
            $quantity = $item->getQuantiteProduct();
            $totalPrice += $product->getPrix() * $quantity;
        }
    
        return $this->render('cart.html.twig', [
            'controller_name' => 'CartController',
            'panier' => $panier,
            // 'totalPrice' => $totalPrice
        ]);
    }
}
