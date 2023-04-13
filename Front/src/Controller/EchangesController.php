<?php

namespace App\Controller;

use App\Entity\Echanges;
use App\Form\Echanges1Type;
use App\Repository\EchangesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/echanges')]
class EchangesController extends AbstractController
{
    #[Route('/', name: 'app_echanges_index', methods: ['GET'])]
    public function index(EchangesRepository $echangesRepository): Response
    {
          
        return $this->render('echanges/index.html.twig', [
            'echanges' => $echangesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_echanges_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EchangesRepository $echangesRepository): Response
    {
        $echange = new Echanges();
        $form = $this->createForm(Echanges1Type::class, $echange);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $echangesRepository->save($echange, true);

            return $this->redirectToRoute('app_echanges_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('echanges/new.html.twig', [
            'echange' => $echange,
            'form' => $form,
        ]);
    }
  
}
