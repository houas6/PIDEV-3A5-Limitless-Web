<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/produit')]
class ProduitController extends AbstractController
{
    #[Route('/', name: 'app_produit_index', methods: ['GET'])]
    public function index(ProduitRepository $produitRepository): Response
    {
        return $this->render('produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    /**
 * @Route("/produit/new", name="produit_new", methods={"GET","POST"})
 */
public function new(Request $request): Response
{
    $produit = new Produit();
    $form = $this->createForm(ProduitType::class, $produit);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();

        // Set the values of the Produit object
        $produit->setNomProduit($data->getNomProduit());
        $produit->setPrix($data->getPrix());
        $produit->setDescription($data->getDescription());
        $produit->setIdUser($data->getId_Produit());
        $produit->setImage($data->getImage());
        $produit->setIdCategorie($data->getIdCategorie());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($produit);
        $entityManager->flush();

        return $this->redirectToRoute('produit_index');
    }

    return $this->render('produit/new.html.twig', [
        'produit' => $produit,
        'form' => $form->createView(),
    ]);
}


public function show(Request $request, Produit $produit): Response
{
    return $this->render('produit/show.html.twig', [
        'produit' => $produit,
    ]);
}



#[Route('/{id_produit}/edit', name: 'app_produit_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Produit $produit): Response
{
    $form = $this->createForm(ProduitType::class, $produit, [
        'action' => $this->generateUrl('app_produit_edit', ['id_produit' => $produit->getId_Produit()])
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('produit/edit.html.twig', [
        'produit' => $produit,
        'form' => $form,
    ]);
}

#[Route('/{id_produit}', name: 'app_produit_delete', methods: ['POST'])]
public function delete(Request $request, Produit $produit): Response
{
    if ($this->isCsrfTokenValid('delete'.$produit->getId_Produit(), $request->request->get('_token'))) {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($produit);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
}

}
