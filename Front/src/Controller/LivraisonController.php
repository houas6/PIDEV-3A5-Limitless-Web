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
use App\Entity\PdfGeneratorService;
use Doctrine\ORM\EntityManagerInterface;



#[Route('/livraison')]
class LivraisonController extends AbstractController
{
    //index livraison front et back
    #[Route('/', name: 'app_livraison_index', methods: ['GET'])]
    public function index(LivraisonRepository $livraisonRepository): Response
    {
        return $this->render('livraison/index.html.twig', [
            'livraisons' => $livraisonRepository->findAll(),
        ]);
    }
    #[Route('/back', name: 'app_livraison_back_index', methods: ['GET'])]
    public function indexback(LivraisonRepository $livraisonRepository): Response
    {
        return $this->render('livraisonback/index.html.twig', [
            'livraisons' => $livraisonRepository->findAll(),
        ]);
    }
    //new livraison front et back
    #[Route('/new', name: 'app_livraison_new', methods: ['GET', 'POST'])]
    public function new(Request $request, LivraisonRepository $livraisonRepository): Response
    {
        $livraison = new Livraison();
        $form = $this->createForm(Livraison1Type::class, $livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $livraisonRepository->save($livraison, true);

            return $this->redirectToRoute('app_livraison_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livraison/new.html.twig', [
            'livraison' => $livraison,
            'form' => $form,
        ]);
    }
    #[Route('/newback', name: 'app_livraison_back_new', methods: ['GET', 'POST'])]
    public function newback(Request $request, LivraisonRepository $livraisonRepository): Response
    {
        $livraison = new Livraison();
        $form = $this->createForm(Livraison1backType::class, $livraison);
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
// show back front
    #[Route('/{idLivraison}/back', name: 'app_livraison_back_show', methods: ['GET'])]
    public function showback(Livraison $livraison): Response
    {
        return $this->render('livraisonback/show.html.twig', [
            'livraison' => $livraison,
        ]);
    }
    #[Route('/{idLivraison}', name: 'app_livraison_show', methods: ['GET'])]
    public function show(Livraison $livraison): Response
    {
        return $this->render('livraison/show.html.twig', [
            'livraison' => $livraison,
        ]);
    }
    //editback
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
    //delete back
    #[Route('/{idLivraison}', name: 'app_livraison_back_delete', methods: ['POST'])]
    public function delete(Request $request, Livraison $livraison, LivraisonRepository $livraisonRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livraison->getIdLivraison(), $request->request->get('_token'))) {
            $livraisonRepository->remove($livraison, true);
        }

        return $this->redirectToRoute('app_livraison_back_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/pdf/livraison', name: 'generator_service')]
public function pdfService(EntityManagerInterface $entityManager): Response
{ 
    $LivraisonRepository = $entityManager->getRepository(Livraison::class);
    $Livraison= $LivraisonRepository->findAll();

    $html =$this->renderView('pdf/index.html.twig', ['Livraison' => $Livraison]);
    $pdfGeneratorService=new PdfGeneratorService();
    $pdf = $pdfGeneratorService->generatePdf($html);

    return new Response($pdf, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="document.pdf"',
    ]);
}


   
}
