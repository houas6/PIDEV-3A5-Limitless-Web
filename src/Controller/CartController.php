<?php

namespace App\Controller;
use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
class CartController extends AbstractController
{
  

    #[Route('/cart', name: 'app_cart')]
    public function index(): Response
    {
        $idUsercon=1;
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
        $count=count($panier);
    
        return $this->render('cart.html.twig', [
            'controller_name' => 'CartController',
            'panier' => $panier,
            'totalPrice' => $totalPrice,
            'panier_count' => $count
        ]);
    }
 /**
 * @Route("/cart/remove/{idPanier}",name="delete_panier")
 * @Method({"DELETE"})
 */
public function delete($idPanier) {
    $idUsercon=1;
    $entityManager=$this->getDoctrine()->getManager();
    $panier = $entityManager->getRepository(Panier::class)->findOneBy([
        'idPanier' => $idPanier
    ]);

    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->remove($panier);
    $entityManager->flush();

    $response = new Response();
    $response->send();
    return $this->redirectToRoute('app_cart');
    } 



     /**
 * @Route("/cart/increment/{idProduit}",name="increment_panier")
 * @Method({"increment"})
 */
public function increment($idProduit) {
    $idUsercon = 1;
    $entityManager=$this->getDoctrine()->getManager();
    $paniers = $entityManager->getRepository(Panier::class)->findBy([
        'idUser' => $idUsercon,
        'idProduit' => $idProduit
    ]);

    foreach ($paniers as $panier) {
        $quantite = $panier->getQuantiteProduct();
            $panier->setQuantiteProduct($quantite + 1);
            $entityManager->persist($panier);
            $entityManager->flush();
    }
    return $this->redirectToRoute('app_cart');
    } 
       


  /**
 * @Route("/cart/decrement/{idProduit}",name="decrement_panier")
 * @Method({"decrement"})
 */
public function decrement($idProduit) {
    $idUsercon = 1;
    $entityManager = $this->getDoctrine()->getManager();
    $paniers = $entityManager->getRepository(Panier::class)->findBy([
        'idUser' => $idUsercon,
        'idProduit' => $idProduit
    ]);

    foreach ($paniers as $panier) {
        $quantite = $panier->getQuantiteProduct();
        if ($quantite > 1) {
            $panier->setQuantiteProduct($quantite - 1);
            $entityManager->persist($panier);
            $entityManager->flush();
        }
    }

    return $this->redirectToRoute('app_cart');
    } 
       
}