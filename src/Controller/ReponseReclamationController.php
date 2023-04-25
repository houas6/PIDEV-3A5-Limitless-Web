<?php

namespace App\Controller;

use App\Entity\ReponseReclamation;
use App\Form\ReponseReclamationType;
use App\Repository\ReponseReclamationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/reponse/reclamation')]

class ReponseReclamationController extends AbstractController
{
   /* #[Route('/', name: 'app_reponse_reclamation_index', methods: ['GET'])]
    public function index(
        ReponseReclamationRepository $reponseReclamationRepository
    ): Response {
        return $this->render('reponse_reclamation/index.html.twig', [
            'reponse_reclamations' => $reponseReclamationRepository->findAll(),
        ]);
    }*/

    /////affichage exercices avec pagination
#[Route('/', name: 'app_reponse_reclamation_index', methods: ['GET'])]
public function index(ReponseReclamationRepository $reponseReclamationRepository, Request $request, PaginatorInterface $paginator): Response
{
    $reponse_reclamations = $reponseReclamationRepository->findAll();
    //la méthode utilise le PaginatorInterface pour paginer les résultats. Le nombre d'éléments par page est fixé à 2.
    $reponse_reclamations = $paginator->paginate($reponse_reclamations, $request->query->getInt('page', 1), 2);

    return $this->render('reponse_reclamation/index.html.twig', ['reponse_reclamations' =>$reponse_reclamations 
       
    ]);
}

    #[Route('/pdf', name: 'pdft', methods: ['GET'])]
    public function index_pdf( ReponseReclamationRepository $reponseRepository,Request $request) {
        $dompdf = new Dompdf();
        ///ajouter notre logo personnalisé
        $logo = file_get_contents("C:\Users\achra\Desktop\Reclamation\public\img\oo.jpg ");
         $logobase64 = base64_encode($logo); //convertir le logo en une chaîne base64
        $reponse = $reponseRepository->findAll();
        ////Ajout de la liste des reponses et le logo personnalisé dans le pdf
        $html = $this->renderView('reponse_reclamation/pdf_file.html.twig', [
            'reponse' => $reponse,
          'logobase64' => $logobase64,
        ]);

        //chargement du pdf
        $dompdf->loadHtml($html); //charger le HTML généré.
        $dompdf->setPaper('A4', 'portrait'); // définir le format de papier sur "A4" en mode "portrait".
        $dompdf->render(); //pour générer le PDF

        $output = $dompdf->output(); //obtenir le contenu du fichier PDF généré.

        $dompdf->stream('list.pdf', ['Attachement' => false]); //télécharger le fichier PDF à l'utilisateur.
        return $this->render('reponse_reclamation/index.html.twig', [
            'reponses' => $reponseRepository->findAll(),
        ]);
    }





    #[Route('/new', name: 'app_reponse_reclamation_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        ReponseReclamationRepository $reponseReclamationRepository
    ): Response {
        $reponseReclamation = new ReponseReclamation();
        $form = $this->createForm(
            ReponseReclamationType::class,
            $reponseReclamation
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reponseReclamationRepository->save($reponseReclamation, true);

            return $this->redirectToRoute(
                'app_reponse_reclamation_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('reponse_reclamation/new.html.twig', [
            'reponse_reclamation' => $reponseReclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reponse_reclamation_show', methods: ['GET'])]
    public function show(ReponseReclamation $reponseReclamation): Response
    {
        return $this->render('reponse_reclamation/show.html.twig', [
            'reponse_reclamation' => $reponseReclamation,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_reponse_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        ReponseReclamation $reponseReclamation,
        ReponseReclamationRepository $reponseReclamationRepository
    ): Response {
        $form = $this->createForm(
            ReponseReclamationType::class,
            $reponseReclamation
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reponseReclamationRepository->save($reponseReclamation, true);

            return $this->redirectToRoute(
                'app_reponse_reclamation_index',
                [],
                Response::HTTP_SEE_OTHER
            );
        }

        return $this->renderForm('reponse_reclamation/edit.html.twig', [
            'reponse_reclamation' => $reponseReclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_reponse_reclamation_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        ReponseReclamation $reponseReclamation,
        ReponseReclamationRepository $reponseReclamationRepository
    ): Response {
        if (
            $this->isCsrfTokenValid(
                'delete' . $reponseReclamation->getId(),
                $request->request->get('_token')
            )
        ) {
            $reponseReclamationRepository->remove($reponseReclamation, true);
        }

        return $this->redirectToRoute(
            'app_reponse_reclamation_index',
            [],
            Response::HTTP_SEE_OTHER
        );
    }
}
