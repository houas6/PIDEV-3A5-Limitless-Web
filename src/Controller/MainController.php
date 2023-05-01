<?php

namespace App\Controller;

use HWI\Bundle\OAuthBundle\Security\Http\ResourceOwnerMap;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Utilisateur;
use App\Form\UtilisateurType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use HWI\Bundle\OAuthBundle\Controller\ConnectController;
use App\Repository\UtilisateurRepository;
use Knp\Component\Pager\PaginatorInterface;


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

    #[Route('/connexion', name: 'app_login')]
public function login(Request $request): Response
{
    $form = $this->createFormBuilder()
        ->add('mail', EmailType::class, [
            'label' => 'Adresse email',
            'attr' => [
                'placeholder' => 'Votre adresse email'
            ]
        ])
        ->add('password', PasswordType::class, [
            'label' => 'Mot de passe',
            'attr' => [
                'placeholder' => 'Votre mot de passe'
            ]
        ])
        ->getForm();

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $formData = $form->getData();
        $mail = $formData['mail'];
        $password = $formData['password'];

        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(Utilisateur::class)->findOneBy(['mail' => $mail]);

        if (!$user || $user->getPassword() !== $password) {
            $this->addFlash('error', 'Email ou mot de passe incorrect.');
            return $this->redirectToRoute('app_login');
        }

        // Vérifie si l'utilisateur est bloqué
        if ($user->isBloque()) {
            $this->addFlash('error', 'Vous êtes bloqué. Veuillez contacter le support.');
            return $this->redirectToRoute('app_login');
        }

        $session = $request->getSession();
        $session->set('user', $user);

        if ($user->getRole() == 'admin') {
            return $this->redirectToRoute('app_back');
        } else {
            return $this->redirectToRoute('app_base');
        }
    }

    return $this->render('main/login.html.twig', [
        'form' => $form->createView(),
    ]);
}


    #[Route('/inscription', name: 'app_ajouter')]
public function indexAjouter(Request $request): Response
{
    $user = new Utilisateur();
    $form = $this->createForm(UtilisateurType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_base');
    }

    return $this->render('main/register.html.twig', [
        'f' => $form->createView()
    ]);
}


    #[Route('/afficherUser', name: 'app_afficher')]
    public function indexAfficher(UtilisateurRepository $utilisateurRepository, Request $request, PaginatorInterface $paginator): Response
    {

        $users = $this->getDoctrine()->getManager()->getRepository(Utilisateur::class)->findAll();
        $us = $utilisateurRepository->findAll();
    //la méthode utilise le PaginatorInterface pour paginer les résultats. Le nombre d'éléments par page est fixé à 2.
    $us = $paginator->paginate($us, $request->query->getInt('page', 1), 2);

    
        return $this->render('main/afficher.html.twig', [
            'u'=>$users,
            'us' => $us
        ]);
    }

    /**
 * @Route("/bloquer/{id}", name="app_bloquer")
 */
public function bloquer(Utilisateur $user): Response
{
    $user->setBloque(true);

    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->persist($user);
    $entityManager->flush();

    $this->addFlash('success', 'L\'utilisateur a été bloqué.');

    return $this->redirectToRoute('app_afficher');
}

/**
 * @Route("/debloquer/{id}", name="app_debloquer")
 */
public function debloquer(Utilisateur $user): Response
{
    $user->debloquer();

    $entityManager = $this->getDoctrine()->getManager();
    $entityManager->flush();

    return $this->redirectToRoute('app_afficher');
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


#[Route('/rechercher-utilisateurs', name: 'app_rechercher_utilisateurs')]
public function rechercherUtilisateurs(UtilisateurRepository $utilisateurRepository, Request $request, PaginatorInterface $paginator): Response
{   
    $query = $request->query->get('q');
    $us = $utilisateurRepository->findAll();
    if ($query) {
        $us = array_filter($us, function ($user) use ($query) {
            return false !== stripos($user->getNom(), $query) || false !== stripos($user->getCin(), $query);
        });
    }
    $us = $paginator->paginate($us, $request->query->getInt('page', 1), 5);
    return $this->render('main/afficher.html.twig', [
        'us' => $us,
    ]);
}

#[Route('/', name: 'app_pagination', methods: ['GET'])]
public function pagination(UtilisateurRepository $utilisateurRepository, Request $request, PaginatorInterface $paginator): Response
{
    
}



}
