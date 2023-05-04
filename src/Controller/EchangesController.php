<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Echanges;
use App\Form\Echanges1Type;
use App\Form\Echanges1backType;
use App\Repository\EchangesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
#[Route('/echanges')]
class EchangesController extends AbstractController
{
    //Afficher back et front
    #[Route('/', name: 'app_echanges_index', methods: ['GET'])]
    public function index(EchangesRepository $echangesRepository): Response
    {
        $idUsercon=1;
        $entityManager=$this->getDoctrine()->getManager();
        $panier = $entityManager->getRepository(Panier::class)->findBy([
            'idUser' => $idUsercon
        ]);
        $count=count($panier);
        return $this->render('echanges/index.html.twig', [
            'echanges' => $echangesRepository->findAll(),
            'panier_count' => $count,
        ]);
    }

    #[Route('/back', name: 'app_echanges_back_index', methods: ['GET'])]
    public function indexback(EchangesRepository $echangesRepository,Request $request , EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
                    $echanges = $entityManager
                    ->getRepository(Echanges::class)
                    ->findAll();



                        $echanges = $paginator->paginate(
                        $echanges,
                        $request->query->getInt('page', 1),2
                        );


        return $this->render('echangesback/index.html.twig', [
            'echanges' => $echanges,
        ]);
    }

//new echanges  front
    #[Route('/new', name: 'app_echanges_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EchangesRepository $echangesRepository): Response
    {
        $echange = new Echanges();
        $form = $this->createForm(Echanges1Type::class, $echange);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $echangesRepository->save($echange, true);
            $echangesRepository->sms();
            $this->addFlash('danger', 'reponse envoyée avec succées');

            return $this->redirectToRoute('app_echanges_index', [], Response::HTTP_SEE_OTHER);
        }
      

        return $this->renderForm('echanges/new.html.twig', [
            'echange' => $echange,
            'form' => $form,
        ]);
    }
    //update echange back
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
    // delete echanges
    #[Route('/{idEchange}', name: 'app_echanges_back_delete', methods: ['POST'])]
    public function delete(Request $request, Echanges $echange, EchangesRepository $echangesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$echange->getIdEchange(), $request->request->get('_token'))) {
            $echangesRepository->remove($echange, true);
        }

        return $this->redirectToRoute('app_echanges_back_index', [], Response::HTTP_SEE_OTHER);
    }
    // show echanges front
    #[Route('/{idEchange}', name: 'app_echanges_back_show', methods: ['GET'])]
    public function show(Echanges $echange): Response
    {
        return $this->render('echangesback/show.html.twig', [
            'echange' => $echange,
        ]);
    }
}
