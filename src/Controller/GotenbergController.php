<?php

namespace App\Controller;

use App\Service\GotenbergService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GotenbergController extends AbstractController
{
    private GotenbergService $gotenbergService;

    public function __construct(GotenbergService $gotenbergService)
    {
        $this->gotenbergService = $gotenbergService;
    }

    #[Route('/convert-url-to-pdf', name: 'convert_url_to_pdf')]
    public function convertUrlToPdf(Request $request): Response
    {
        $url = $request->query->get('url');

        if (!$url) {
            return new Response('URL manquante.', Response::HTTP_BAD_REQUEST);
        }

        try {
            $pdfContent = $this->gotenbergService->convertUrlToPdf($url);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response($pdfContent, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="converted.pdf"',
        ]);
    }

    #[Route("/generate-pdf-test", name: "generate_pdf-test")]
    public function generatePdf(): Response
    {
        $htmltopdfContent = '
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <title>Document PDF</title>
  </head>
  <body>
    <h1>Test PDF généré.</h1>
  </body>
</html>';

        try {
            $pdfContent = $this->gotenbergService->convertHtmlToPdf($htmltopdfContent);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response($pdfContent, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="document.pdf"',
        ]);
    }

    #[Route("/upload-file-to-pdf", name: "upload_file_to_pdf", methods: ["POST"])]
    public function uploadFileToPdf(Request $request): Response
    {
        $file = $request->files->get('file');

        if (!$file) {
            return new Response('Aucun fichier envoyé.', Response::HTTP_BAD_REQUEST);
        }

        try {
            $pdfContent = $this->gotenbergService->convertFileToPdf($file);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response($pdfContent, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="converted.pdf"',
        ]);
    }
}
