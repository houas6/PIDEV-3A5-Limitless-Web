<?php

namespace App\Controller;

use App\Entity\Livreur;
use App\Form\LivreurType;
use App\Repository\LivreurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
#[Route('/livreurback')]
class LivreurControllerback extends AbstractController
{
//****************** mobile ********************
//************afficher******************
#[Route('/afficheMob', name: 'afficheMob')]
    public function show_mobile(LivreurRepository $livreurRepository,NormalizerInterface $normalizer): Response
    {
        $livreurs=$livreurRepository->findAll();
        $jsonContent = $normalizer->normalize($livreurs, 'json', ['groups' => 'post:read']);
        dump( $jsonContent);
        return new Response(json_encode($jsonContent));
       
    }
//***************ajouter*************
    #[Route('/AddjsonM/{nom}/{mail}/{telephone}', name: 'app_addjsonM')]
    public function addjson(Request $request, $nom,$mail,$telephone, EntityManagerInterface $entityManager)
    {
        $livreur = new Livreur();
        $nomLivreur = $request->query->get("nom");
        $mailLivreur = $request->query->get("mail");
        $telephoneLivreur = $request->query->get("telephone");
        // Set the properties of the $produit object directly
        $livreur->setNom($nom);
        $livreur->setMail($mail);
        $livreur->setTelephone($telephone);
        $entityManager->persist($livreur);
        $entityManager->flush();

        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($livreur);
        return new JsonResponse($formatted);
    }
//**************modifier*******************
#[Route("/modifierM", name:"modifM")]   
public function updateMaisonMobile(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
{
    $livreurRepository = $entityManager->getRepository(Livreur::class);
    $livreur = $livreurRepository->find($request->get('ID_livreur'));

    if (!$livreur) {
        throw $this->createNotFoundException('Le livreur avec l\'ID '.$request->get('ID_livreur').' n\'existe pas.');
    }

    $livreur->setNom($request->get('nom'));
    $livreur->setMail($request->get('mail'));
    $livreur->setTelephone($request->get('telephone'));
    $entityManager->flush();

    $jsonContent = $serializer->serialize($livreur, 'json', ['groups' => 'categorie']);
    return new Response('Le livreur a été modifié avec succès : '.$jsonContent);
}

//***************supprimer********************
#[Route("/deleteM", name:"supprimeM")]
public function delete_mobile(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
{
    try {
        $id = $request->query->get("ID_livreur");
        $livreurRepository = $entityManager->getRepository(Livreur::class);

        $livreur = $livreurRepository->find($id);

        if ($livreur !== null) {
            $entityManager->remove($livreur);
            $entityManager->flush();
            $formatted = $serializer->serialize($livreur, 'json');
            return new Response($formatted);
        }
    } catch (\Exception $e) {
        return new Response(" you have to delete all products attached to this categorie, firstly ");
    }

    return new Response(" livreur does not exist ");
}

//***************web***************************
    #[Route('/', name: 'app_livreur_back_index', methods: ['GET'])]
    public function index(LivreurRepository $livreurRepository): Response
    {
        return $this->render('livreurback/index.html.twig', [
            'livreurs' => $livreurRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_livreur_back_new', methods: ['GET', 'POST'])]
    public function new(Request $request, LivreurRepository $livreurRepository): Response
    {
        $livreur = new Livreur();
        $form = $this->createForm(LivreurType::class, $livreur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $livreurRepository->save($livreur, true);

            return $this->redirectToRoute('app_livreur_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livreurback/new.html.twig', [
            'livreur' => $livreur,
            'form' => $form,
        ]);
    }

    #[Route('/{idLivreur}', name: 'app_livreur_back_show', methods: ['GET'])]
    public function show(Livreur $livreur): Response
    {
        return $this->render('livreurback/show.html.twig', [
            'livreur' => $livreur,
        ]);
    }

    #[Route('/{idLivreur}/editback', name: 'app_livreur_back_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livreur $livreur, LivreurRepository $livreurRepository): Response
    {
        $form = $this->createForm(LivreurType::class, $livreur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $livreurRepository->save($livreur, true);

            return $this->redirectToRoute('app_livreur_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livreurback/edit.html.twig', [
            'livreur' => $livreur,
            'form' => $form,
        ]);
    }

    #[Route('/{idLivreur}', name: 'app_livreur_back_delete', methods: ['POST'])]
    public function delete(Request $request, Livreur $livreur, LivreurRepository $livreurRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livreur->getIdLivreur(), $request->request->get('_token'))) {
            $livreurRepository->remove($livreur, true);
        }

        return $this->redirectToRoute('app_livreur_back_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/', name: 'app_livreur_index1', methods: ['GET','POST'])]
    public function index1(EntityManagerInterface $entityManager, Request $request, LivreurRepository $livreurRepository): Response
{
    $livreurs = $entityManager->getRepository(Livreur::class)->findAll();
    $back = null;

    if ($request->isMethod("POST")) {
        if ($request->request->get('optionsRadios')) {
            $SortKey = $request->request->get('optionsRadios');
            switch ($SortKey) {
                case 'nom':
                    $livreurs = $livreurRepository->SortBynom();
                    break;
                case 'mail':
                    $livreurs = $livreurRepository->SortBymail();
                    break;
                case 'telephone':
                    $livreurs = $livreurRepository->SortBytelephone();
                    break;
            }
        } else {
            $type = $request->request->get('optionsearch');
            $value = $request->request->get('Search');
            switch ($type) {
                case 'nom':
                    $livreurs = $livreurRepository->findBynom($value);
                    break;
                case 'mail':
                    $livreurs = $livreurRepository->findBymail($value);
                    break;
                case 'telephone':
                    $livreurs = $livreurRepository->findBytelephone($value);
                    break;
            }
        }

        if ($livreurs) {
            $back = "success";
        } else {
            $back = "failure";
        }
    }

    return $this->render('livreurback/index.html.twig', [
        'livreurs' => $livreurs,
        'back' => $back
    ]);
}

}
