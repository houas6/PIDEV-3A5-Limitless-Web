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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use App\Form\ForgetPasswordType;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;





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
public function sendEmail(MailerInterface $mailer,$mail,$url): Response
    {
        
        $mailcontent="<p> Bonjour</p> unde demande de réinitialisation de mot de passe a été effectuée. Veuillez cliquer sur le lien suivant :".$url;
        
        $email = (new Email())
            ->from('alarassaa147@gmail.com')
            ->to($mail)
            ->subject('mot de passe oublie - AutoDoc')
            ->text('Mot de passe oublie AutoDoc')
            ->html($mailcontent);
    
        try {
            $mailer->send($email);
            $message = 'Email sent successfully!';
        } catch (\Exception $e) {
            $message = 'An error occurred while sending the email: ' . $e->getMessage();
        }
    
        return new Response($message);
    }
#[Route('/', name: 'app_pagination', methods: ['GET'])]
public function pagination(UtilisateurRepository $utilisateurRepository, Request $request, PaginatorInterface $paginator): Response
{
    
}

 /**
     * @Route("/forgot", name="forgot")
     */
    public function forgotPassword(Request $request, UtilisateurRepository $userRepository,MailerInterface $mailer, TokenGeneratorInterface  $tokenGenerator)
    {


        $form = $this->createForm(ForgetPasswordType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $donnees = $form->getData();//


            $user = $userRepository->findOneBy(['mail'=>$donnees]);
            if(!$user) {
                $this->addFlash('danger','cette adresse n\'existe pas');
                return $this->redirectToRoute("forgot");

            }
            $token = $tokenGenerator->generateToken();

            try{
                $user->setResetToken($token);
                $entityManger = $this->getDoctrine()->getManager();
                $entityManger->persist($user);
                $entityManger->flush();




            }catch(\Exception $exception) {
                $this->addFlash('warning','une erreur est survenue :'.$exception->getMessage());
                return $this->redirectToRoute("app_login");


            }

            $url = $this->generateUrl('app_reset_password',array('token'=>$token),UrlGeneratorInterface::ABSOLUTE_URL);

            //BUNDLE MAILER
            
            //send mail
            $this->sendEmail($mailer,$user->getMail(),$url);
            $this->addFlash('message','E-mail  de réinitialisation du mp envoyé :');
        //    return $this->redirectToRoute("app_login");



        }

        return $this->render("main/forgotPassword.html.twig",['form'=>$form->createView()]);
    }

     /**
     * @Route("/resetpassword/{token}", name="app_reset_password")
     */
    public function resetpassword(Request $request,string $token, UserPasswordEncoderInterface  $passwordEncoder)
    {
        $user = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(['reset_token'=>$token]);

        if($user == null ) {
            $this->addFlash('danger','TOKEN INCONNU');
            return $this->redirectToRoute("app_login");

        }

        if($request->isMethod('POST')) {
            $user->setResetToken(null);
            $password = $request->request->get('password');
            $user->setPassword($password);
            $entityManger = $this->getDoctrine()->getManager();
            $entityManger->persist($user);
            $entityManger->flush();

            $this->addFlash('message','Mot de passe mis à jour :');
            return $this->redirectToRoute("app_login");

        }
        else {
            return $this->render("main/resetPassword.html.twig",['token'=>$token]);

        }
    }

}
