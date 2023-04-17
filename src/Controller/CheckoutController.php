<?php

namespace App\Controller;
use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Form\CommandeType;
use App\Entity\Utilisateur;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'app_checkout')]
    public function checkout(Request $request): Response
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

        $commande = new Commande();
$form = $this->createForm(CommandeType::class, $commande);
$form->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
    $commande = $form->getData();
    $commande->setTotal($totalPrice + 10);
    $commande->setIdUser($utilisateur);
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->persist($commande);
    $entityManager->flush();
    return $this->redirectToRoute("app_stripe");

}


        

        
        return $this->render('checkout.html.twig', [
            'controller_name' => 'CartController',
            'form' => $form->createView(),
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

    #[Route('/email', name: 'app_mail')]
    public function sendEmail(MailerInterface $mailer): Response
    {
    
        $email = (new Email())
            ->from('alarassaa147@gmail.com')
            ->to('rassaaala@gmail.com')
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');
    
        try {
            $mailer->send($email);
            $message = 'Email sent successfully!';
        } catch (\Exception $e) {
            $message = 'An error occurred while sending the email: ' . $e->getMessage();
        }
    
        return new Response($message);
    }


    // #[Route('/stripe', name: 'app_stripe')]
    // public function index(): Response
    // {
    //     return $this->render('stripe/index.html.twig', [
    //         'stripe_key' => $_ENV["STRIPE_KEY"],
    //     ]);
    // }
 
 
    // #[Route('/stripe/create-charge', name: 'app_stripe_charge', methods: ['POST'])]
    // public function createCharge(Request $request)
    // {
    //     $idUsercon=1;
    //     $entityManager=$this->getDoctrine()->getManager();
    //     $utilisateur=$entityManager->getRepository(Utilisateur::class)->findOneBy([
    //         'idUser' => $idUsercon
    //     ]);
    //     $panier = $entityManager->getRepository(Panier::class)->findBy([
    //         'idUser' => $idUsercon
    //     ]);
    //     $totalPrice = 0;

    //     foreach ($panier as $item) {
    //         $product = $item->getIdProduit();
    //         $quantity = $item->getQuantiteProduct();
    //         $totalPrice += $product->getPrix() * $quantity;
    //     }
    //     Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
    //     Stripe\Charge::create ([
    //             "amount" => ($totalPrice+10) * 100,
    //             "currency" => "usd",
    //             "source" => $request->request->get('stripeToken'),
    //             "description" => "Binaryboxtuts Payment Test"
    //     ]);
    //     $this->addFlash(
    //         'success',
    //         'Payment Successful!'
    //     );
    //     return $this->redirectToRoute('app_stripe', [], Response::HTTP_SEE_OTHER);
    // }

}
