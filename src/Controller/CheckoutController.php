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

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'app_checkout')]
    public function index(): Response
    {
        $idUsercon=1;
        $entityManager=$this->getDoctrine()->getManager();
        $utilisateur=$entityManager->getRepository(Utilisateur::class)->findOneBy([
            'idUser' => $idUsercon
        ]);
        $panier = $entityManager->getRepository(Panier::class)->findBy([
            'idUser' => $idUsercon
        ]);
        $count=count($panier);
        $totalPrice = 0;

        foreach ($panier as $item) {
            $product = $item->getIdProduit();
            $quantity = $item->getQuantiteProduct();
            $totalPrice += $product->getPrix() * $quantity;
        }
        $nom=$utilisateur->getNom();
        $prenom=$utilisateur->getPrenom();
        $adresse=$utilisateur->getAdresse();
        $numero=$utilisateur->getNumero();
        $mail=$utilisateur->getMail();

    
        return $this->render('checkout.html.twig', [
            'controller_name' => 'CartController',
            'panier' => $panier,    
            'totalPrice' => $totalPrice,
            'panier_count' => $count,
            'nom'=> $nom,
            'prenom'=> $prenom,
            'adresse'=> $adresse,
            'numero'=> $numero,
            'mail'=> $mail,

        ]);
    }
}
