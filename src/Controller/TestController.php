<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class TestController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

  

    #[Route('/bask', name: 'app_bask')]
    public function index2(): Response
    {
        $entityManager=$this->getDoctrine()->getManager();
        $utilisateur = $entityManager->getRepository(Utilisateur::class)->find(1);
        $produit = $entityManager->getRepository(Produit::class)->find(16);
        // Create a new Basket entity
        $panier = $entityManager->getRepository(Panier::class)->findOneBy([
            'idUser' => $utilisateur,
            'idProduit' => $produit
        ]);
    
        if ($panier) {
            // If the product is already in the cart, increment the quantity
            $quantite = $panier->getQuantiteProduct();
            $panier->setQuantiteProduct($quantite + 1);
        } else {
            // If the product is not in the cart, create a new Basket entity with quantity = 1
            $panier = new Panier();
            $panier->setIdUser($utilisateur);
            $panier->setIdProduit($produit);
            $panier->setQuantiteProduct(1);
        }
        

        // Persist the entity to the database
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($panier);
        $entityManager->flush();

        // Return a response to indicate success
        return new Response('Basket entity created successfully');
    }

    #[Route('/produit/{id}', name: 'test_id_produit')]
    public function show(int $id, ProduitRepository $rep)
    {
        $entityManager=$this->getDoctrine()->getManager();
       
        $produit = $entityManager->getRepository(Produit::class)->find($id);

         if (!$produit) {
             throw $this->createNotFoundException('Article not found');
        }

        return $this->render('test/index.html.twig', [
            'produit' => $produit,
        ]);
    }





    #[Route('/panier/{idUser}', name: 'test_panieruser')]
    public function showpanier(int $idUser): Response
    { 
        $entityManager=$this->getDoctrine()->getManager();
        $panier = $entityManager->getRepository(Panier::class)->findBy([
            'idUser' => $idUser
        ]);
        $totalPrice = 0;

    foreach ($panier as $item) {
        $product = $item->getIdProduit();
        $quantity = $item->getQuantiteProduct();
        $totalPrice += $product->getPrix() * $quantity;
    }

        return $this->render('test/index.html.twig', [
            'panier' => $panier,
            'totalPrice' => $totalPrice
        ]);
    }

    

//     #[Route('/bask1', name: 'app_bask1')]
//     public function index3(): Response
// {
//     // Get all Basket entities from the database
//     $entityManager = $this->getDoctrine()->getManager();
//     $basketRepository = $entityManager->getRepository(Basket::class);
//     $baskets = $basketRepository->findAll();

//     // Do something with the entities
//     // For example, you could output them to the browser
//     $response = '';
//     foreach ($baskets as $basket) {
//         $response .= $basket->getIdClient()->getNomu() . ' - ' . $basket->getIdArticle()->getNoma() . ' - ' . $basket->getDateAjout()->format('Y-m-d H:i:s') . '<br>';
//     }

//     // Return a response to indicate success
//     return new Response($response);
// }

// #[Route('/bask2', name: 'app_bask2')]
// public function viewBasket(BasketRepository $basketRepository)
// {
//     $basketData = $basketRepository->findAll();

//     // dd ($basketData);

//     return $this->render('testingServices.html.twig', [
//          'basketData' => $basketData,
//      ]);
// }



// #[Route('/bask3', name: 'app_bask3')]
// public function viewBasket2( BasketService $basketService)
// {
//     $basketData = $basketService->getCartItems(32);

//     return $this->render('testingServices.html.twig', [
//          'basketData' => $basketData,
//      ]);
// }

}
