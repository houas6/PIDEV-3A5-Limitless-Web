<?php

namespace App\Controller;

use App\Entity\Echanges;
use App\Form\Echanges1Type;
use App\Form\Echanges1backType;
use App\Repository\EchangesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Produit;
use App\Repository\ProduitRepository;

#[Route('/echanges')]
class EchangesController extends AbstractController
{
    
    //***********json**************
    private $echangesRepository;
    private $normalizer;

    public function __construct(
        EchangesRepository $echangesRepository,
        NormalizerInterface $normalizer
    ) {
        $this->echangesRepository = $echangesRepository;
        $this->normalizer = $normalizer;
    }
    //************afficher******************
        #[Route('/afficheMobe', name: 'afficheMobe')]
        public function show_mobile(): Response
        {
            $echanges = $this->echangesRepository->findAll();
        $jsonContent = $this->normalizer->normalize($echanges, 'json', ['echanges' => 'echanges:read']);

        $produitEchange = [];
        $produitOffert = [];
        $idproduit = [];
        $idechange=[];
        $statut=[];
        $commentaire=[];
        foreach ($jsonContent as $echange) {
            if (isset($echange['produitEchange'])) {
                $produitEchange[] = $echange['produitEchange']['idproduit'];
            }
            if (isset($echange['produitOffert'])) {
                $produitOffert[] = $echange['produitOffert']['idproduit'];
            }
            $idechange[]=$echange['idEchange'];
            $statut[]=$echange['statut'];
            $commentaire[]=$echange['commentaire'];
        }
        $idproduit = array_merge($produitEchange, $produitOffert);

        $result=[];
        foreach($idechange as $key => $ide){
            $result[]=array("idEchange"=>$ide,"statut"=>$statut[$key],"commentaire"=>$commentaire[$key],"produitEchange"=>$produitEchange[$key],"produitOffert"=>$produitOffert[$key]);
        }

        return new Response(json_encode($result));
        
        }
        //***************show*****************
        #[Route("/showonech", name: "showonech")]
        public function cartid(Request $req, SerializerInterface $serializer, ProduitRepository $repo, EntityManagerInterface $entityManager)
        {
            // Récupérer les produits correspondants
            $produitEchange = $req->get('produit_echange');
            $produitOffert = $req->get('produit_offert');
            $echanges = $entityManager->getRepository(Echanges::class)->findBy([
                'produit_echange' => $produitEchange,
                'produit_offert' => $produitOffert,
            ]);
    
            // Formater les résultats pour la réponse
            $formatred = [];
            foreach ($echanges as $item) {
                $formatred[] = [
                    'idEchange' => $item->getidEchange()->getidEchange(),
                    'produit_echange' => $item->getProduitOffert()->getProduitOffert(),
                    'produit_offert' => $item->getProduitEchange()->getProduitEchange(),
                    'statut' => $item->getStatut(),
                    'commentaire' => $item->getCommentaire(),
                ];
            }
    
            // Sérialiser les résultats en JSON et retourner la réponse
            $jsonData = $serializer->serialize($formatred, 'json');
            return new Response($jsonData);
        }
    
        //***************ajouter*************
        #[Route('/AddjsonMe/{produit_echange}/{produit_offert}/{statut}/{commentaire}', name: 'app_addjsonMe')]
        public function addjson(Request $request, EntityManagerInterface $entityManager)
        {
            $echange = new Echanges();
        
            // Retrieve the product ids from the URL parameters
            $idProduitech = $request->get('produit_echange');
            $idProduitof = $request->get('produit_offert');
        
            // Get the product objects from the repository
            $produitech = $entityManager->getRepository(Produit::class)->find($idProduitech);
            $produitof = $entityManager->getRepository(Produit::class)->find($idProduitof);
        
            // Set the properties of the $echange object directly
            $echange->setProduitEchange($produitech);
            $echange->setProduitOffert($produitof);
            $echange->setStatut($request->get('statut') ?? 'en cours');
            $echange->setCommentaire($request->get('commentaire') ?? '');
        
            $entityManager->persist($echange);
            $entityManager->flush();
        
            // Serialize the $echange object and return it as JSON
            $serializer = new Serializer([new ObjectNormalizer()]);
            $formatted = $serializer->normalize($echange);
            return new JsonResponse($formatted);
        }
        
    //**************modifier*******************
    #[Route("/modifierMe", name:"modifMe")]   
    public function updateMaisonMobile(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
        $EchangesRepository = $entityManager->getRepository(Echanges::class);
        $echange = $EchangesRepository->find($request->get('id_echange'));
        // Retrieve the product ids from the URL parameters
        $idProduitech = $request->get('produit_echange');
        $idProduitof = $request->get('produit_offert');

        // Get the product objects from the repository
        $produitech = $entityManager->getRepository(Produit::class)->find($idProduitech);
        $produitof = $entityManager->getRepository(Produit::class)->find($idProduitof);
        if (!$echange) {
            throw $this->createNotFoundException('Lechange avec l\'ID '.$request->get('id_echange').' n\'existe pas.');
        }
        
        $echange->setProduitEchange($produitech);
        $echange->setProduitOffert($produitof);
        $echange->setStatut($request->get('statut'));
        $echange->setCommentaire($request->get('commentaire'));
        $entityManager->flush();
            //arj3elha mbaed
        $jsonContent = $serializer->serialize($echange, 'json', ['groups' => 'echanges']);
        return new Response('Lechange a été modifié avec succès : '.$jsonContent);
    }
    //**************supprimer*******************
    #[Route("/deleteMe", name:"supprimeMe")]
    public function delete_mobile(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): Response
    {
        try {
            $id = $request->query->get("id_echanges");
            $EchangesRepository = $entityManager->getRepository(Echanges::class);

            $echange = $EchangesRepository->find($id);

            if ($echange !== null) {
                $entityManager->remove($echange);
                $entityManager->flush();
                $formatted = $serializer->serialize($echange, 'json');
                return new Response($formatted);
            }
        } catch (\Exception $e) {
            return new Response(" you have to delete echanges, firstly ");
        }

        return new Response(" echange does not exist ");
    }

    //Afficher back et front
    #[Route('/', name: 'app_echanges_index', methods: ['GET'])]
    public function index(EchangesRepository $echangesRepository): Response
    {
          
        return $this->render('echanges/index.html.twig', [
            'echanges' => $echangesRepository->findAll(),
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
