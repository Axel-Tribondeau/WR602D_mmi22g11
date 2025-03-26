<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Repository\FileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;

class HistoryController extends AbstractController
{
    #[Route('/history', name: 'app_history')]
    public function index(FileRepository $fileRepository): Response
    {
        $user = $this->getUser();
        $startOfDay = new DateTime('today');
        $endOfDay = new DateTime('tomorrow');

        $files = $fileRepository->findBy(['userkeyId' => $user], ['createdAt' => 'DESC']);
        $pdfCount = $fileRepository->countPdfGeneratedByUserOnDate($user->getId(), $startOfDay, $endOfDay);

        $subscription = $user->getSubscription();
        $maxPdfPerDay = $subscription?->getMaxPdf() ?? 5;

        return $this->render('history/index.html.twig', [
            'files' => $files,
            'pdfCount' => $pdfCount,
            'maxPdfPerDay' => $maxPdfPerDay,
        ]);
    }

    #[Route('/download/{fileName}', name: 'download_pdf')]
    public function download(string $fileName): Response
    {
        $tempDir = $this->getParameter('kernel.project_dir') . '/public/temp/';
        $filePath = $tempDir . $fileName;

        if (!file_exists($filePath)) {
            throw new NotFoundHttpException('Le fichier n\'existe pas');
        }

        return new Response(
            file_get_contents($filePath),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]
        );
    }

    #[Route('/clear-history', name: 'clear_history', methods: ['POST'])]
    public function clearHistory(FileRepository $fileRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $files = $fileRepository->findBy(['userkeyId' => $user]);

        foreach ($files as $file) {
            $filePath = $this->getParameter('kernel.project_dir') . '/public/temp/' . $file->getName();
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            $entityManager->remove($file);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Votre historique a été effacé avec succès.');

        return $this->redirectToRoute('app_history');
    }
}
