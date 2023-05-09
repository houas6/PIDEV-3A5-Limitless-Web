<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Entity\Utilisateur;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;




class MobileController extends AbstractController
{
    /**
     * @Route("/mobile", name="register")
     */
    public function register(Request $request): Response
    {
        $nom = $request->query->get("nom");
        $prenom = $request->query->get("prenom");
        $adresse = $request->query->get("adresse");
        $numero = $request->query->get("numero");
        $cin= $request->query->get("cin");
        $mail = $request->query->get("mail");
        $password = $request->query->get("password");
        $role = $request->query->get("role");
        
        $user=new Utilisateur();
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setAdresse($adresse);
        $user->setNumero($numero);
        $user->setCin($cin);
        $user->setMail($mail);
        $user->setPassword($password);
        $user->setRole($role);
        // Appel de l'API de création d'utilisateur
        try {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
        return new JsonResponse("Account is created",200);
        }
        catch(Exception $ex)
        {
            return new Response("Exception",$ex->getMessage());
        }
        
        // Redirection vers la page de profil de l'utilisateur nouvellement créé
        return $this->redirectToRoute('profile');
    }

    /**
 * @Route("/mobile/signin", name="signin")
 */
public function signin(Request $request, SessionInterface $session): Response
{
    $mail = $request->query->get("mail");
    $password = $request->query->get("password");

    // Recherche de l'utilisateur par e-mail et mot de passe
    $userRepository = $this->getDoctrine()->getRepository(Utilisateur::class);
    $user = $userRepository->findOneBy([
        'mail' => $mail,
        'password' => $password,
    ]);

    // Si l'utilisateur n'est pas trouvé, retourner une erreur
    if (!$user) {
        return new Response("Echec d'authentification: adresse e-mail ou mot de passe incorrect");
    }
    else {
        $serializer= new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($user);
        return new JsonResponse($formatted);

    }

    // Stockage des informations utilisateur dans la session
    $session->set('user_id', $user->getIdUser());
    $session->set('user_role', $user->getRole());

    // Redirection vers la page de profil de l'utilisateur connecté
    return $this->redirectToRoute('app_afficher');
    
}

}

