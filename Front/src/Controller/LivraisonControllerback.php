<?php

namespace App\Controller;

use App\Entity\Livraison;
use App\Form\Livraison1Type;
use App\Form\Livraison1backType;
use App\Repository\LivraisonRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/livraisonback')]
class LivraisonControllerback extends AbstractController
{
    #[Route('/', name: 'app_livraison_back_index', methods: ['GET'])]
    public function index(LivraisonRepository $livraisonRepository): Response
    {
        return $this->render('livraisonback/index.html.twig', [
            'livraisons' => $livraisonRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_livraison_back_new', methods: ['GET', 'POST'])]
    public function new(Request $request, LivraisonRepository $livraisonRepository): Response
    {
        $livraison = new Livraison();
        $form = $this->createForm(Livraison1Type::class, $livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $livraisonRepository->save($livraison, true);

            return $this->redirectToRoute('app_livraison_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livraisonback/new.html.twig', [
            'livraison' => $livraison,
            'form' => $form,
        ]);
    }

    #[Route('/{idLivraison}', name: 'app_livraison_back_show', methods: ['GET'])]
    public function show(Livraison $livraison): Response
    {
        return $this->render('livraisonback/show.html.twig', [
            'livraison' => $livraison,
        ]);
    }

    #[Route('/{idLivraison}/editback', name: 'app_livraison_back_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Livraison $livraison, LivraisonRepository $livraisonRepository): Response
    {
        $form = $this->createForm(Livraison1backType::class, $livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $livraisonRepository->save($livraison, true);

            return $this->redirectToRoute('app_livraison_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livraisonback/edit.html.twig', [
            'livraison' => $livraison,
            'form' => $form,
        ]);
    }

    #[Route('/{idLivraison}', name: 'app_livraison_back_delete', methods: ['POST'])]
    public function delete(Request $request, Livraison $livraison, LivraisonRepository $livraisonRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livraison->getIdLivraison(), $request->request->get('_token'))) {
            $livraisonRepository->remove($livraison, true);
        }

        return $this->redirectToRoute('app_livraison_back_index', [], Response::HTTP_SEE_OTHER);
    }
}
