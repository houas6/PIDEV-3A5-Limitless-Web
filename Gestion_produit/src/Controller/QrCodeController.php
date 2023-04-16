<?php

namespace App\Controller;

use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Doctrine\Persistence\ManagerRegistry;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Label\Font\NotoSans;
use Doctrine\ORM\EntityManagerInterface;
 

class QrCodeController extends AbstractController
{
    #[Route('/qr-codes/{id_produit}', name: 'app_qr_codes', methods: ['GET'])]
    public function index(string $id_produit, EntityManagerInterface $entityManager): Response
    {
        // Retrieve the product from the database
        $produit = $entityManager->getRepository(Produit::class)->find($id_produit);

        // Generate the QR code using the product name as the data
        $qrCode = QrCode::create($produit->getNomproduit())
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(120)
            ->setMargin(0)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        // Generate the different types of QR codes
        $writer = new PngWriter();
        
        $label = Label::create('')->setFont(new NotoSans(8));

        $qrCodes = [];
        $qrCodes['img'] = $writer->write($qrCode)->getDataUri();
        $qrCodes['simple'] = $writer->write(
        $qrCode,
        null,
        $label->setText('Simple')
        )->getDataUri();


        $qrCode->setForegroundColor(new Color(255, 0, 0));
        $qrCodes['changeColor'] = $writer->write(
            $qrCode,
            null,
            $label->setText('Color Change')
        )->getDataUri();

        $qrCode->setForegroundColor(new Color(0, 0, 0))->setBackgroundColor(new Color(255, 0, 0));
        $qrCodes['changeBgColor'] = $writer->write(
            $qrCode,
            null,
            $label->setText('Background Color Change')
        )->getDataUri();

        $qrCode->setSize(200)->setForegroundColor(new Color(0, 0, 0))->setBackgroundColor(new Color(255, 255, 255));
        $qrCodes['withImage'] = $writer->write(
            $qrCode,
            null,
            $label->setText('With Image')->setFont(new NotoSans(20))
        )->getDataUri();
        
        // Pass the generated QR codes and product data to the template
        $templateData = [
            'qrCodes' => $qrCodes,
            'produit' => $produit
        ];

        return $this->render('qr_code/index.html.twig', $templateData);
    }
}
