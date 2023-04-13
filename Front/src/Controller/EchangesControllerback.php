<?php

namespace App\Controller;

use App\Entity\Echanges;
use App\Form\Echanges1backType;
use App\Repository\EchangesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/echangesback')]
class EchangesControllerback extends AbstractController
{
    #[Route('/', name: 'app_echanges_back_index', methods: ['GET'])]
    public function index(EchangesRepository $echangesRepository): Response
    {
        return $this->render('echangesback/index.html.twig', [
            'echanges' => $echangesRepository->findAll(),
        ]);
    }

    #[Route('/{idEchange}', name: 'app_echanges_back_show', methods: ['GET'])]
    public function show(Echanges $echange): Response
    {
        return $this->render('echangesback/show.html.twig', [
            'echange' => $echange,
        ]);
    }

    #[Route('/{idEchange}/editback', name: 'app_echanges_back_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Echanges $echange, EchangesRepository $echangesRepository): Response
    {
        $form = $this->createForm(Echanges1backType::class, $echange);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $echangesRepository->save($echange, true);

            return $this->redirectToRoute('app_echanges_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('echangesback/edit.html.twig', [
            'echange' => $echange,
            'form' => $form,
        ]);
    }

    #[Route('/{idEchange}', name: 'app_echanges_back_delete', methods: ['POST'])]
    public function delete(Request $request, Echanges $echange, EchangesRepository $echangesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$echange->getIdEchange(), $request->request->get('_token'))) {
            $echangesRepository->remove($echange, true);
        }

        return $this->redirectToRoute('app_echanges_back_index', [], Response::HTTP_SEE_OTHER);
    }
}
