<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Entity\Utilisateur;
use Symfony\Component\HttpFoundation\JsonResponse;


class MobileController extends AbstractController
{
    /**
     * @Route("/register/mobile", name="register")
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
}

