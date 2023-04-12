<?php

namespace App\Controller;

use App\Entity\Commande;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class BackController extends AbstractController
{
    #[Route('/back', name: 'app_back')]
    public function index(): Response
    {
        $commande = $this->getDoctrine()->getManager()->getRepository(Commande::class)->findAll();
        return $this->render('commandeback.html.twig', [
            'commande'=>$commande
        ]);
    }
    #[Route('/supprimerUser/{id}', name: 'app_supprimer')]
    public function indexSupprimer(Commande $c): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($c);
        $em->flush();

        return $this->redirectToRoute('app_back');
    }
    /**
 * @Route("/commande/{id}", name="app_changer_status")
 */
public function app_changer_status(Request $request, Commande $commande)
{
    // get selected value from dropdown
    $status = $request->request->get('status');

    $commande->setStatus($status);
    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->persist($commande);
    $entityManager->flush();

    return $this->redirectToRoute('app_back');
}
}
