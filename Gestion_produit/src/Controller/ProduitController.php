<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;


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
public function new(Request $request, EntityManagerInterface $entityManager, ProduitRepository $repository): Response
{
    $produit = new Produit();
    $form = $this->createForm(ProduitType::class, $produit);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Get the uploaded file
        $file = $form->get('image')->getData();

        if ($file) {
            // Use the actual filename of the uploaded file
            $filename = $file->getClientOriginalName();

            // Move the file to the uploads directory
            $file->move($this->getParameter('uploads'), $filename);

            // Save the filename to the database
            $produit->setImage($filename);
        }

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
public function edit(Request $request, Produit $produit, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(ProduitType::class, $produit, [
        'action' => $this->generateUrl('app_produit_edit', ['id_produit' => $produit->getId_Produit()])
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        
        $entityManager->flush();

        return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('produit/edit.html.twig', [
        'produit' => $produit,
        'form' => $form,
    ]);
}

#[Route('/{id_produit}', name: 'app_produit_delete', methods: ['POST'])]
public function delete(Request $request, Produit $produit,EntityManagerInterface $entityManager): Response
{
    if ($this->isCsrfTokenValid('delete'.$produit->getId_Produit(), $request->request->get('_token'))) {
        
        $entityManager->remove($produit);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_produit_index', [], Response::HTTP_SEE_OTHER);
}

#[Route('/showProduct', name: 'app_produit_index1', methods: ['GET','POST'])]
    public function index1(EntityManagerInterface $entityManager,Request $request,ProduitRepository $produitRepository): Response
    {
        $produits = $entityManager
            ->getRepository(Produit::class)
            ->findAll();

            /////////
            $back = null;
            
            if($request->isMethod("POST")){
                if ( $request->request->get('optionsRadios')){
                    $SortKey = $request->request->get('optionsRadios');
                    switch ($SortKey){
                        case 'nomproduit':
                            $produits = $produitRepository->SortBynomProduit();
                            break;
    
                        case 'description':
                            $produits = $produitRepository->SortBydescriptionProduit();
                            break;

                        
    
    
                    }
                }
                else
                {
                    $type = $request->request->get('optionsearch');
                    $value = $request->request->get('Search');
                    switch ($type){
                        
    
                        case 'description':
                            $produits = $produitRepository->findBydescriptionProduit($value);
                            break;
    
                        case 'nomproduit':
                            $produits = $produitRepository->findBynomProduit($value);
                            break;
    
                    }
                }

                if ( $produits){
                    $back = "success";
                }else{
                    $back = "failure";
                }
            }
                ////////

        return $this->render('produit/showProduct.html.twig', [
            'produits' => $produits, 'back'=> $back
        ]);
    }

}
