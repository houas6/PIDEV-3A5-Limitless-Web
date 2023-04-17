<?php

namespace App\Controller;

use Dompdf\Dompdf;
use App\Entity\Panier;
use App\Entity\Produit;
use App\Entity\Commande;
use App\Form\CommandeType;
use App\Entity\Utilisateur;
use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

class PdfController extends AbstractController
{
    #[Route('/pdf', name: 'app_pdf')]
    public function index(): Response
    {
        return $this->render('pdf/index.html.twig', [
            'controller_name' => 'PdfController',
        ]);
    }


   
    #[Route('/pdf1', name: 'app_pdf1')]
public function genererTicketAchatPDF(Request $request)
{
    $idUsercon=1;
    $entityManager=$this->getDoctrine()->getManager();
    $panier = $entityManager->getRepository(Panier::class)->findBy([
        'idUser' => $idUsercon
    ]);
    $template = 'ticket_achat.html.twig'; // Replace with the name of your Twig template
 
    try {
        // Create a new instance of Dompdf
        $dompdf = new Dompdf();

        // Render the PDF content using Twig
        $html = $this->renderView($template, [
            'date' => new \DateTime(),
            'produits' => $panier,
        ]);

        // Load the HTML content into Dompdf
        $dompdf->loadHtml($html);

        // Set the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the PDF
        $dompdf->render();

        // Return the PDF as a response
        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="ticket_achat.pdf"',
            ]
        );
    } catch (\Exception $ex) {
        // Handle any errors that occur during PDF generation
        return new Response('Erreur lors de la génération du PDF: '.$ex->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}

}
