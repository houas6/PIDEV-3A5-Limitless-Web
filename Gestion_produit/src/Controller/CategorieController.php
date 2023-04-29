<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
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

#[Route('/categorie')]
class CategorieController extends AbstractController
{
    

    #[Route('/afficheMob', name: 'afficheMob')]
    public function show_mobile(CategorieRepository $categorieRepository,NormalizerInterface $normalizer): Response
    {
        $categories=$categorieRepository->findAll();
        $jsonContent = $normalizer->normalize($categories, 'json', ['groups' => 'post:read']);
        dump( $jsonContent);
        return new Response(json_encode($jsonContent));
       
    }

#[Route('/AddjsonM/{nom}', name: 'app_addjsonM')]
public function addjson(Request $request,$nom)
{
    $categorie = new Categorie();
    $nomCategorie = $request->query->get("nomCategorie");
    

    // Set the properties of the $produit object directly
    $categorie->setnomCategorie($nom);
    
    $em = $this->getDoctrine()->getManager();

    $em->persist($categorie);
    $em->flush();

    $serializer = new Serializer([new ObjectNormalizer()]);
    $formatted = $serializer->normalize($categorie);
    return new JsonResponse($formatted);
}
///////
#[Route("/modifierM", name:"modifM")]
    
public function updateMaisonMobile(Request $request, SerializerInterface $serializer): Response
{
    $em = $this->getDoctrine()->getManager();
    $categorie = $em->getRepository(Categorie::class)->find($request->get('idcategorie'));

    if (!$categorie) {
        throw $this->createNotFoundException('La categorie avec l\'ID '.$request->get('idcategorie').' n\'existe pas.');
    }

    $categorie->setNomCategorie($request->get('nomCategorie'));
    

    $em->flush();

    $jsonContent = $serializer->serialize($categorie, 'json', ['groups' => 'categorie']);
    return new Response('La categorie a été modifiée avec succès : '.$jsonContent);
}
#[Route("/deleteM", name:"supprimeM")]
     
    
public function delete_mobile(Request $request, SerializerInterface $serializer ,EntityManagerInterface $entityManager): Response
    {   
        try{
        $id = $request->query->get("idcategorie");
    $entityManager = $this->getDoctrine()->getManager();
    $categorieRepository = $entityManager->getRepository(Categorie::class);
    $produitRepository = $entityManager->getRepository(Produit::class);
    /* $produits_categorie=$produitRepository->findBy(["idCategorie"=>$id]);
     foreach($produits_categorie as $produit){
        $entityManager->remove($produit);
        $entityManager->flush();
     }*/
    $categorie = $categorieRepository->find($id);

    if ($categorie !== null) {
        $entityManager->remove($categorie);
        $entityManager->flush();
        $formatted = $serializer->serialize($categorie, 'json');
        return new Response($formatted);

    }
    }catch(\Exception $e){
        return new Response(" you have to delete all products attached to this categorie ,firstly "); 
    }
    

          

        return new Response(" categorie does not exist ");
    }
    #[Route('/', name: 'app_categorie_index', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository): Response
    {
        return $this->render('categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }
   
    #[Route('/new', name: 'app_categorie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategorieRepository $categorieRepository): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorieRepository->save($categorie, true);

            return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    #[Route('/{idcategorie}', name: 'app_categorie_show', methods: ['GET'])]
    public function show(Categorie $categorie): Response
    {
        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    #[Route('/{idcategorie}/edit', name: 'app_categorie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Categorie $categorie, CategorieRepository $categorieRepository): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $categorieRepository->save($categorie, true);

            return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    #[Route('/{idcategorie}', name: 'app_categorie_delete', methods: ['POST'])]
    public function delete(Request $request, Categorie $categorie, CategorieRepository $categorieRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getIdcategorie(), $request->request->get('_token'))) {
            $categorieRepository->remove($categorie, true);
        }

        return $this->redirectToRoute('app_categorie_index', [], Response::HTTP_SEE_OTHER);
    }
}
