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
    public function index(Request $request): Response
    {
        $repository = $this->getDoctrine()->getManager()->getRepository(Commande::class);
        $entityManager=$this->getDoctrine()->getManager();
        
        $search = $request->query->get('q');
        $sort = $request->query->get('sort');
        if ($search) {

            $commande = $entityManager->getRepository(Commande::class)->createQueryBuilder('c')
            ->where('c.nom LIKE :search')
            ->orWhere('c.adresse LIKE :search')
            ->orWhere('c.prenom LIKE :search')
            ->setParameter('search', '%'.$search.'%')
            ->getQuery()
            ->getResult();
        
        } elseif ($sort) {
            $direction = $sort == 'desc' ? 'DESC' : 'ASC';
            $commande = $repository->findBy([], ['total' => $direction]);
        } else {
            $commande = $repository->findAll();
        }
    
        return $this->render('commandeback.html.twig', [
            'commande' => $commande,
            'search' => $search,
            'sort' => $sort 
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
