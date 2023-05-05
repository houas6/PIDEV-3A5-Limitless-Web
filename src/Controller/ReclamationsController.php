<?php

namespace App\Controller;

use Dompdf\Dompdf;
use App\Entity\Panier;
use App\Entity\Reclamations;
use App\Form\ReclamationsType;
use App\Repository\ReclamationRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\ReponseReclamationRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/reclamations')]
class ReclamationsController extends AbstractController
{
    #[Route('/', name: 'app_reclamations_index', methods: ['GET'])]
    public function index(
        ReclamationRepository $reclamationRepository
    ): Response {


        
        $idUsercon=1;
        $entityManager=$this->getDoctrine()->getManager();
        $panier = $entityManager->getRepository(Panier::class)->findBy([
            'idUser' => $idUsercon
        ]);
        $count=count($panier);
        return $this->render('reclamations/index.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
            'panier_count' => $count,
        ]);
    }
    #[Route('/appjoin/{id}', name: 'app_join', methods: ['GET'])]
    public function affapp(
        ReponseReclamationRepository $reponseReclamationRepository,
        $id
    ): Response {
        return $this->render('reclamations/appjoin.html.twig', [
            'reclamations' => $reponseReclamationRepository->findBy([
                'id' => $id,
            ]),
        ]);
    }

    #[Route('/rechercheReclamation', name: 'app_reclamation_recherche')]
    public function rechercheReclamation(
        ReclamationRepository $reclamationRepository,
        Request $request
    ): Response {
        $data = $request->get('search');
        $reclamation = $reclamationRepository->searchQB($data);
        return $this->render('reclamations/index.html.twig', [
            'reclamations' => $reclamation,
        ]);
    }

    #[Route('/new', name: 'app_reclamations_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        ReclamationRepository $reclamationRepository
    ): Response {
        $idUsercon=1;
        $entityManager=$this->getDoctrine()->getManager();
        $panier = $entityManager->getRepository(Panier::class)->findBy([
            'idUser' => $idUsercon
        ]);
        $count=count($panier);
        $reclamation = new Reclamations();
        $form = $this->createForm(ReclamationsType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamationRepository->save($reclamation, true);
            $reclamationRepository->sms();
            $this->addFlash('danger', 'reponse envoyée avec succées');
            return $this->redirectToRoute(
                'app_reclamations_index',
                [],
                
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('reclamations/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
            'panier_count' => $count,
        ]);
    }

    #[Route('/{id}', name: 'app_reclamations_show', methods: ['GET'])]
    public function show(Reclamations $reclamation): Response
    {
        return $this->render('reclamations/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reclamations_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Reclamations $reclamation,
        ReclamationRepository $reclamationRepository
    ): Response {
        $form = $this->createForm(ReclamationsType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reclamationRepository->save($reclamation, true);

            return $this->redirectToRoute(
                'app_reclamations_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('reclamations/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reclamations_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        Reclamations $reclamation,
        ReclamationRepository $reclamationRepository
    ): Response {
        if (
            $this->isCsrfTokenValid(
                'delete' . $reclamation->getId(),
                $request->request->get('_token')
            )
        ) {
            $reclamationRepository->remove($reclamation, true);
        }

        return $this->redirectToRoute(
            'app_reclamations_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
    #[Route("/createReclamations/{etat}/{description}/", name: "addReclamationsJSON", methods: ['POST'])]
    public function addReclamationsJSON(
        Request $request,
        NormalizerInterface $normalizer
    ): JsonResponse {
        $entityManager = $this->getDoctrine()->getManager();

        $reclamation = new Reclamations();
        $reclamation->setEtat($request->get('etat'));
        $reclamation->setDescription($request->get('description'));

        $entityManager->persist($reclamation);
        $entityManager->flush();

        $jsonContent = $normalizer->normalize($reclamation, 'json', [
            'groups' => 'reclamations',
        ]);

        return new JsonResponse($jsonContent, JsonResponse::HTTP_CREATED);
    }

    #[Route('/mobile/listReclamtion/', name: 'mobile_listReclamtion')]
    public function listReclamtion(
        Request $request,
        ReclamationRepository $reclamationRepository
    ): Response {
        $existingReclamations = $reclamationRepository->findAll();

        if (!empty($existingReclamations)) {
            $serializer = new Serializer([new ObjectNormalizer()]);
            $formatted = [];

            foreach ($existingReclamations as $reclamation) {
                $formatted[] = $serializer->normalize([
                    'idReclamation' => $reclamation->getId(),
                    'etat' => $reclamation->getEtat(),
                    'description' => $reclamation->getDescription(),
                ]);
            }

            return new JsonResponse($formatted);
        } else {
            return new JsonResponse([]);
        }
    }
    #[Route("/mobile/deleteReclamation/{id}/", name: "deleteReclamationJSON")]
    public function deleteReclamationJSON(int $id)
    {
        $em = $this->getDoctrine()->getManager();
        $reclamation = $em->getRepository(Reclamations::class)->find($id);

        if (!$reclamation) {
            throw $this->createNotFoundException(
                'No reclamation found for id ' . $id
            );
        }

        $em->remove($reclamation);
        $em->flush();

        return new JsonResponse(
            ['message' => 'reclamation deleted successfully'],
            JsonResponse::HTTP_OK
        );
    }
    #[Route("/mobile/updateReclamation/{id}/{description}/{etat}/", name: "updateReclamationJSON")]
    public function updateReclamationJSON(
        Request $req,
        NormalizerInterface $Normalizer,
        int $id
    ) {
        $em = $this->getDoctrine()->getManager();
        $reclamation = $em->getRepository(Reclamations::class)->find($id);

        if (!$reclamation) {
            throw $this->createNotFoundException(
                'No reclamation found for id ' . $id
            );
        }

        $reclamation->setDescription($req->get('description'));
        $reclamation->setEtat($req->get('etat'));

        $em->persist($reclamation);
        $em->flush();

        $jsonContent = $Normalizer->normalize($reclamation, 'json', [
            'Groups' => 'reclamations',
        ]);
        return new JsonResponse($jsonContent, JsonResponse::HTTP_OK);
    }

    #[Route('/mobile/listReclamtion/etat/{etat}', name: 'mobile_listReclamtionEtat')]
    public function listReclamtionEtat(
        Request $request,
        ReclamationRepository $reclamationRepository,
        string $etat // new parameter
    ): Response {
        $existingReclamations = $reclamationRepository->findBy([
            'etat' => $etat,
        ]);

        if (!empty($existingReclamations)) {
            $serializer = new Serializer([new ObjectNormalizer()]);
            $formatted = [];

            foreach ($existingReclamations as $reclamation) {
                $formatted[] = $serializer->normalize([
                    'idReclamation' => $reclamation->getId(),
                    'etat' => $reclamation->getEtat(),
                    'description' => $reclamation->getDescription(),
                ]);
            }

            return new JsonResponse($formatted);
        } else {
            return new JsonResponse([]);
        }
    }
}
