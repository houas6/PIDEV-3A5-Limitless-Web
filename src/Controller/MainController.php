<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Utilisateur;
use App\Form\UtilisateurType;

class MainController extends AbstractController
{
    #[Route('/base', name: 'app_base')]
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/back', name: 'app_back')]
    public function indexBack(): Response
    {
        return $this->render('back.html.twig', [
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

    #[Route('/register', name: 'app_ajouter')]
public function indexAjouter(Request $request): Response
{
    $user = new Utilisateur();
    $form = $this->createForm(UtilisateurType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_afficher');
    }

    return $this->render('main/register.html.twig', [
        'f' => $form->createView()
    ]);
}


    #[Route('/afficherUser', name: 'app_afficher')]
    public function indexAfficher(): Response
    {

        $users = $this->getDoctrine()->getManager()->getRepository(Utilisateur::class)->findAll();
        return $this->render('main/afficher.html.twig', [
            'u'=>$users
        ]);
    }

    #[Route('/supprimerUser/{id}', name: 'app_supprimer')]
    public function indexSupprimer(Utilisateur $user): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('app_afficher');
    }

   /**
 * @Route("/modifierUser/{id}", name="app_modifier")
 */
public function indexModifier(Request $request, $id): Response
{
    $user = $this->getDoctrine()->getManager()->getRepository(Utilisateur::class)->find($id);

    $form = $this->createForm(UtilisateurType::class, $user);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        return $this->redirectToRoute('app_afficher');
    }

    return $this->render('main/modifierUser.html.twig', ['f' => $form->createView()]);
}

}
