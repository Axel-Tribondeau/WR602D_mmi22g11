<?php
// src/Controller/HistoryController.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Repository\FileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HistoryController extends AbstractController
{
    #[Route('/history', name: 'app_history')]
    public function index(FileRepository $fileRepository): Response
    {
        $user = $this->getUser();
        $startOfDay = new \DateTime('today');
        $endOfDay = new \DateTime('tomorrow');

        $files = $fileRepository->findBy(['userkey' => $user], ['createdAt' => 'DESC']);
        $pdfCount = $fileRepository->countPdfGeneratedByUserOnDate($user->getId(), $startOfDay, $endOfDay);

        $subscription = $user->getSubscription();
        $maxPdfPerDay = $subscription?->getMaxPdf() ?? 5;


        return $this->render('history/index.html.twig', [
            'files' => $files,
            'pdfCount' => $pdfCount,
            'maxPdfPerDay' => $maxPdfPerDay,
        ]);
    }
}