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
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Validator\Constraints\DateTime;
use App\Entity\Utilisateur;

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
    public function new(MailerInterface $mailer,
        Request $request,
        ReponseReclamationRepository $reponseReclamationRepository
    ): Response {
        $idUsercon=1;
        $entityManager=$this->getDoctrine()->getManager();
        $utilisateur=$entityManager->getRepository(Utilisateur::class)->findOneBy([
            'idUser' => $idUsercon
        ]);
        $reponseReclamation = new ReponseReclamation();
        $form = $this->createForm(
            ReponseReclamationType::class,
            $reponseReclamation
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->sendEmail($mailer,$utilisateur,$reponseReclamation);
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
    
    public function sendEmail(MailerInterface $mailer,$utilisateur,$ReponseReclamation): Response
    {
        
        $currentDate = new \DateTime();
        
        $expirationDate = new \DateTime('+1 year');
        
        $nom = $utilisateur->getNom();
        $prenom = $utilisateur->getPrenom();
        $reponse=$ReponseReclamation->getContenu();
         $mailContent = "<!DOCTYPE html>\n"
                . "\n"
                . "<html lang=\"en\" xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:v=\"urn:schemas-microsoft-com:vml\">\n"
                . "\n"
                . "<head>\n"
                . "	<title></title>\n"
                . "	<meta content=\"text/html; charset=utf-8\" http-equiv=\"Content-Type\" />\n"
                . "	<meta content=\"width=device-width, initial-scale=1.0\" name=\"viewport\" />\n"
                . "	<!--[if mso]><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch><o:AllowPNG/></o:OfficeDocumentSettings></xml><![endif]-->\n"
                . "	<!--[if !mso]><!-->\n"
                . "	<link href=\"https://fonts.googleapis.com/css?family=Lato\" rel=\"stylesheet\" type=\"text/css\" />\n"
                . "	<!--<![endif]-->\n"
                . "	<style>\n"
                . "		* {\n"
                . "			box-sizing: border-box;\n"
                . "		}\n"
                . "\n"
                . "		body {\n"
                . "			margin: 0;\n"
                . "			padding: 0;\n"
                . "		}\n"
                . "\n"
                . "		a[x-apple-data-detectors] {\n"
                . "			color: inherit !important;\n"
                . "			text-decoration: inherit !important;\n"
                . "		}\n"
                . "\n"
                . "		#MessageViewBody a {\n"
                . "			color: inherit;\n"
                . "			text-decoration: none;\n"
                . "		}\n"
                . "\n"
                . "		p {\n"
                . "			line-height: inherit\n"
                . "		}\n"
                . "\n"
                . "		@media (max-width:670px) {\n"
                . "			.icons-inner {\n"
                . "				text-align: center;\n"
                . "			}\n"
                . "\n"
                . "			.icons-inner td {\n"
                . "				margin: 0 auto;\n"
                . "			}\n"
                . "\n"
                . "			.row-content {\n"
                . "				width: 100% !important;\n"
                . "			}\n"
                . "\n"
                . "			.column .border {\n"
                . "				display: none;\n"
                . "			}\n"
                . "\n"
                . "			.stack .column {\n"
                . "				width: 100%;\n"
                . "				display: block;\n"
                . "			}\n"
                . "		}\n"
                . "	</style>\n"
                . "</head>\n"
                . "\n"
                . "<body style=\"background-color: #F5F5F5; margin: 0; padding: 0; -webkit-text-size-adjust: none; text-size-adjust: none;\">\n"
                . "	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"nl-container\" role=\"presentation\"\n"
                . "		style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #F5F5F5;\" width=\"100%\">\n"
                . "		<tbody>\n"
                . "			<tr>\n"
                . "				<td>\n"
                . "					<table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"row row-1\"\n"
                . "						role=\"presentation\" style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt;\" width=\"100%\">\n"
                . "						<tbody>\n"
                . "							<tr>\n"
                . "								<td>\n"
                . "									<table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"\n"
                . "										class=\"row-content stack\" role=\"presentation\"\n"
                . "										style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000000; width: 650px;\"\n"
                . "										width=\"650\">\n"
                . "										<tbody>\n"
                . "											<tr>\n"
                . "												<td class=\"column column-1\"\n"
                . "													style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 5px; padding-bottom: 5px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;\"\n"
                . "													width=\"100%\">\n"
                . "													<div class=\"spacer_block\"\n"
                . "														style=\"height:30px;line-height:30px;font-size:1px;\"> </div>\n"
                . "												</td>\n"
                . "											</tr>\n"
                . "										</tbody>\n"
                . "									</table>\n"
                . "								</td>\n"
                . "							</tr>\n"
                . "						</tbody>\n"
                . "					</table>\n"
                . "					<table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"row row-2\"\n"
                . "						role=\"presentation\" style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt;\" width=\"100%\">\n"
                . "						<tbody>\n"
                . "							<tr>\n"
                . "								<td>\n"
                . "									<table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"\n"
                . "										class=\"row-content stack\" role=\"presentation\"\n"
                . "										style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt; background-color: #D6E7F0; color: #000000; width: 650px;\"\n"
                . "										width=\"650\">\n"
                . "										<tbody>\n"
                . "											<tr>\n"
                . "												<td class=\"column column-1\"\n"
                . "													style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-left: 25px; padding-right: 25px; padding-top: 5px; padding-bottom: 60px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;\"\n"
                . "													width=\"100%\">\n"
                . "													<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"text_block\"\n"
                . "														role=\"presentation\"\n"
                . "														style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;\"\n"
                . "														width=\"100%\">\n"
                . "														<tr>\n"
                . "															<td\n"
                . "																style=\"padding-left:15px;padding-right:10px;padding-top:20px;\">\n"
                . "																<div style=\"font-family: sans-serif\">\n"
                . "																	<div\n"
                . "																		style=\"font-size: 12px; font-family: Lato, Tahoma, Verdana, Segoe, sans-serif; mso-line-height-alt: 18px; color: #052d3d; line-height: 1.5;\">\n"
                . "																		<p\n"
                . "																			style=\"margin: 0; font-size: 14px; text-align: center; mso-line-height-alt: 75px;\">\n"
                . "																			<span style=\"font-size:50px;\"><strong><span\n"
                . "																						style=\"font-size:50px;\"><span\n"
                . "																							style=\"font-size:38px;\">WELCOME\n"
                . "																							TO\n"
                . "																							AUTODOC</span></span></strong></span>\n"
                . "																		</p>\n"
                . "																		<p\n"
                . "																			style=\"margin: 0; font-size: 14px; text-align: center; mso-line-height-alt: 51px;\">\n"
                . "																			<span style=\"font-size:34px;\"><strong><span\n"
                . "																						style=\"font-size:34px;\"><span\n"
                . "																							style=\"color:#2190e3;font-size:34px;\">".$reponse."</span></span></strong></span>\n"
                . "																		</p>\n"
                . "																	</div>\n"
                . "																</div>\n"
                . "															</td>\n"
                . "														</tr>\n"
                . "													</table>\n"
                . "													<table border=\"0\" cellpadding=\"10\" cellspacing=\"0\"\n"
                . "														class=\"text_block\" role=\"presentation\"\n"
                . "														style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;\"\n"
                . "														width=\"100%\">\n"
                . "														<tr>\n"
                . "															<td>\n"
                . "																<div style=\"font-family: sans-serif\">\n"
                . "																	<div\n"
                . "																		style=\"font-size: 12px; mso-line-height-alt: 14.399999999999999px; color: #555555; line-height: 1.2; font-family: Lato, Tahoma, Verdana, Segoe, sans-serif;\">\n"
                . "																		<p\n"
                . "																			style=\"margin: 0; font-size: 14px; text-align: center;\">\n"
                . "																			<span\n"
                . "																				style=\"font-size:18px;color:#000000;\">Thanks\n"
                . "																				for purchasing our service.</span></p>\n"
                . "																		<p\n"
                . "																			style=\"margin: 0; font-size: 14px; text-align: center;\">\n"
                . "																			<span\n"
                . "																				style=\"font-size:18px;color:#000000;\">This\n"
                . "																				mail contains all the infos (do no\n"
                . "																				reply).</span></p>\n"
                . "																		<p\n"
                . "																			style=\"margin: 0; font-size: 14px; text-align: center;\">\n"
                . "																			<span\n"
                . "																				style=\"font-size:18px;color:#000000;\"><br>\n"
                . "																				"."<br>Purchase Date : ".$currentDate->format('Y-m-d')."<br>Expiration Date : ".$expirationDate->format('Y-m-d') ."<br> </span></p>\n"
                . "																		<p\n"
                . "																			style=\"margin: 0; font-size: 14px; text-align: center;\">\n"
                . "																			<span\n"
                . "																				style=\"font-size:18px;color:#000000;\"><br>Details:<br> Company name : "."AutoDoc"."</span>\n"
                . "																		</p>\n"                        
                . "																		<p\n"
                . "																			style=\"margin: 0; font-size: 14px; text-align: center; mso-line-height-alt: 14.399999999999999px;\">\n"
                . "																			 </p>\n"
                . "																	</div>\n"
                . "																</div>\n"
                . "															</td>\n"
                . "														</tr>\n"
                . "													</table>\n"
                . "												</td>\n"
                . "											</tr>\n"
                . "										</tbody>\n"
                . "									</table>\n"
                . "								</td>\n"
                . "							</tr>\n"
                . "						</tbody>\n"
                . "					</table>\n"
                . "					<table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"row row-3\"\n"
                . "						role=\"presentation\" style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt;\" width=\"100%\">\n"
                . "						<tbody>\n"
                . "							<tr>\n"
                . "								<td>\n"
                . "									<table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"\n"
                . "										class=\"row-content stack\" role=\"presentation\"\n"
                . "										style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000000; width: 650px;\"\n"
                . "										width=\"650\">\n"
                . "										<tbody>\n"
                . "											<tr>\n"
                . "												<td class=\"column column-1\"\n"
                . "													style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 20px; padding-bottom: 60px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;\"\n"
                . "													width=\"100%\">\n"
                . "													<table border=\"0\" cellpadding=\"10\" cellspacing=\"0\"\n"
                . "														class=\"text_block\" role=\"presentation\"\n"
                . "														style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;\"\n"
                . "														width=\"100%\">\n"
                . "														<tr>\n"
                . "															<td>\n"
                . "																<div style=\"font-family: sans-serif\">\n"
                . "																	<div\n"
                . "																		style=\"font-size: 12px; mso-line-height-alt: 18px; color: #555555; line-height: 1.5; font-family: Lato, Tahoma, Verdana, Segoe, sans-serif;\">\n"
                . "																		<p\n"
                . "																			style=\"margin: 0; font-size: 14px; text-align: center;\">\n"
                . "																			MasterHR © -  Your favorite company tool.\n"
                . "																		</p>\n"
                . "																		<p\n"
                . "																			style=\"margin: 0; font-size: 14px; text-align: center;\">\n"
                . "																			Tunis, Tunisia.</p>\n"
                . "																	</div>\n"
                . "																</div>\n"
                . "															</td>\n"
                . "														</tr>\n"
                . "													</table>\n"
                . "													<table border=\"0\" cellpadding=\"10\" cellspacing=\"0\"\n"
                . "														class=\"divider_block\" role=\"presentation\"\n"
                . "														style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt;\"\n"
                . "														width=\"100%\">\n"
                . "														<tr>\n"
                . "															<td>\n"
                . "																<div align=\"center\">\n"
                . "																	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"\n"
                . "																		role=\"presentation\"\n"
                . "																		style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt;\"\n"
                . "																		width=\"60%\">\n"
                . "																		<tr>\n"
                . "																			<td class=\"divider_inner\"\n"
                . "																				style=\"font-size: 1px; line-height: 1px; border-top: 1px dotted #C4C4C4;\">\n"
                . "																				<span> </span></td>\n"
                . "																		</tr>\n"
                . "																	</table>\n"
                . "																</div>\n"
                . "															</td>\n"
                . "														</tr>\n"
                . "													</table>\n"
                . "													<table border=\"0\" cellpadding=\"10\" cellspacing=\"0\"\n"
                . "														class=\"text_block\" role=\"presentation\"\n"
                . "														style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;\"\n"
                . "														width=\"100%\">\n"
                . "														<tr>\n"
                . "															<td>\n"
                . "																<div style=\"font-family: sans-serif\">\n"
                . "																	<div\n"
                . "																		style=\"font-size: 12px; mso-line-height-alt: 14.399999999999999px; color: #4F4F4F; line-height: 1.2; font-family: Lato, Tahoma, Verdana, Segoe, sans-serif;\">\n"
                . "																		<p\n"
                . "																			style=\"margin: 0; font-size: 12px; text-align: center;\">\n"
                . "																			<span style=\"font-size:14px;\"><strong>Support\n"
                . "																					:\n"
                . "																					masterhrcomapny@gmail.com</strong></span>\n"
                . "																		</p>\n"
                . "																	</div>\n"
                . "																</div>\n"
                . "															</td>\n"
                . "														</tr>\n"
                . "													</table>\n"
                . "												</td>\n"
                . "											</tr>\n"
                . "										</tbody>\n"
                . "									</table>\n"
                . "								</td>\n"
                . "							</tr>\n"
                . "						</tbody>\n"
                . "					</table>\n"
                . "					<table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"row row-4\"\n"
                . "						role=\"presentation\" style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt;\" width=\"100%\">\n"
                . "						<tbody>\n"
                . "							<tr>\n"
                . "								<td>\n"
                . "									<table align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"\n"
                . "										class=\"row-content stack\" role=\"presentation\"\n"
                . "										style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000000; width: 650px;\"\n"
                . "										width=\"650\">\n"
                . "										<tbody>\n"
                . "											<tr>\n"
                . "												<td class=\"column column-1\"\n"
                . "													style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; vertical-align: top; padding-top: 5px; padding-bottom: 5px; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;\"\n"
                . "													width=\"100%\">\n"
                . "													<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"\n"
                . "														class=\"icons_block\" role=\"presentation\"\n"
                . "														style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt;\"\n"
                . "														width=\"100%\">\n"
                . "														<tr>\n"
                . "															<td\n"
                . "																style=\"vertical-align: middle; color: #9d9d9d; font-family: inherit; font-size: 15px; padding-bottom: 5px; padding-top: 5px; text-align: center;\">\n"
                . "																<table cellpadding=\"0\" cellspacing=\"0\"\n"
                . "																	role=\"presentation\"\n"
                . "																	style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt;\"\n"
                . "																	width=\"100%\">\n"
                . "																	<tr>\n"
                . "																		<td\n"
                . "																			style=\"vertical-align: middle; text-align: center;\">\n"
                . "																			<!--[if vml]><table align=\"left\" cellpadding=\"0\" cellspacing=\"0\" role=\"presentation\" style=\"display:inline-block;padding-left:0px;padding-right:0px;mso-table-lspace: 0pt;mso-table-rspace: 0pt;\"><![endif]-->\n"
                . "																			<!--[if !vml]><!-->\n"
                . "																			<table cellpadding=\"0\" cellspacing=\"0\"\n"
                . "																				class=\"icons-inner\" role=\"presentation\"\n"
                . "																				style=\"mso-table-lspace: 0pt; mso-table-rspace: 0pt; display: inline-block; margin-right: -4px; padding-left: 0px; padding-right: 0px;\">\n"
                . "																				<!--<![endif]-->\n"
                . "																				<tr>\n"
                . "																				</tr>\n"
                . "																			</table>\n"
                . "																		</td>\n"
                . "																	</tr>\n"
                . "																</table>\n"
                . "															</td>\n"
                . "														</tr>\n"
                . "													</table>\n"
                . "												</td>\n"
                . "											</tr>\n"
                . "										</tbody>\n"
                . "									</table>\n"
                . "								</td>\n"
                . "							</tr>\n"
                . "						</tbody>\n"
                . "					</table>\n"
                . "				</td>\n"
                . "			</tr>\n"
                . "		</tbody>\n"
                . "	</table><!-- End -->\n"
                . "</body>\n"
                . "\n"
 		    . "</html>";
        
        $email = (new Email())
            ->from('alarassaa147@gmail.com')
            ->to('achrafjaidane@gmail.com')
            ->subject('Purchase from AutoDoc')
            ->text('Purchase from AutoDoc')
            ->html($mailContent);
    
        try {
            $mailer->send($email);
            $message = 'Email sent successfully!';
        } catch (\Exception $e) {
            $message = 'An error occurred while sending the email: ' . $e->getMessage();
        }
    
        return new Response($message);
    }














}
