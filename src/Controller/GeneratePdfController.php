<?php

namespace App\Controller;

use App\Service\GotenbergService;
use App\Entity\File;
use App\Repository\FileRepository;
use DateTimeImmutable;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GeneratePdfController extends AbstractController
{
    private GotenbergService $pdfService;
    private EntityManagerInterface $entityManager;

    public function __construct(GotenbergService $pdfService, EntityManagerInterface $entityManager)
    {
        $this->pdfService = $pdfService;
        $this->entityManager = $entityManager;
    }

    #[Route('/generate-pdf', name: 'generate-pdf')]
    public function generatePdf(Request $request, FileRepository $fileRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (!$this->checkUserQuota($user, $fileRepository)) {
            return $this->redirectToRoute('app_subscriptions');
        }

        $urlForm = $this->createUrlForm();
        $htmlForm = $this->createHtmlForm();
        $fileForm = $this->createFileForm();

        $urlForm->handleRequest($request);
        $htmlForm->handleRequest($request);
        $fileForm->handleRequest($request);

        if ($urlForm->isSubmitted() && $urlForm->isValid()) {
            return $this->handleUrlForm($urlForm->getData()['url'], $user);
        }

        if ($htmlForm->isSubmitted() && $htmlForm->isValid()) {
            return $this->handleHtmlForm($htmlForm->get('html_content')->getData(), $user);
        }

        if ($fileForm->isSubmitted() && $fileForm->isValid()) {
            return $this->handleFileForm($fileForm->get('file')->getData(), $user);
        }

        return $this->render('generate_pdf/index.html.twig', [
            'urlForm' => $urlForm->createView(),
            'htmlForm' => $htmlForm->createView(),
            'fileForm' => $fileForm->createView(),
        ]);
    }

    private function checkUserQuota($user, FileRepository $fileRepository): bool
    {
        $startOfDay = new DateTime('today');
        $endOfDay = new DateTime('tomorrow');

        $pdfCount = $fileRepository->countPdfGeneratedByUserOnDate($user->getId(), $startOfDay, $endOfDay);

        $subscription = $user->getSubscription();
        $maxPdf = $subscription->getMaxPdf();

        if ($pdfCount >= $maxPdf) {
            $this->addFlash('error', 'Vous avez atteint votre quota de PDF 😢. Découvrez nos autres formules !');
            return false;
        }

        return true;
    }

    private function createUrlForm()
    {
        return $this->createFormBuilder()
            ->add('url', UrlType::class, ['required' => true, 'label' => 'Entrez l\'URL'])
            ->add('submit', SubmitType::class, ['label' => 'Générer le PDF'])
            ->getForm();
    }

    private function createHtmlForm()
    {
        return $this->createFormBuilder()
            ->add('html_content', TextareaType::class, [
                'label' => 'Entrez votre contenu : <br>',
                'label_html' => true,
                'required' => true,
                'attr' => ['rows' => 10, 'cols' => 50, 'class' => 'tinymce']
            ])
            ->add('submit', SubmitType::class, ['label' => 'Générer le PDF'])
            ->getForm();
    }

    private function createFileForm()
    {
        return $this->createFormBuilder()
            ->add('file', FileType::class, [
                'label' => 'Téléchargez un fichier',
                'required' => false,
                'attr' => ['accept' => '.docx,.pptx,.odt,.txt']
            ])
            ->add('submit', SubmitType::class, ['label' => 'Générer le PDF'])
            ->getForm();
    }

    private function handleUrlForm(string $url, $user): Response
    {
        $pdfContent = $this->pdfService->convertUrlToPdf($url);
        $filePath = $this->savePdfFile($pdfContent);

        $this->saveFileRecord($user, $filePath);

        return $this->createPdfResponse($pdfContent);
    }

    private function handleHtmlForm(string $htmlContent, $user): Response
    {
        $pdfContent = $this->pdfService->convertHtmlToPdf($htmlContent);
        $filePath = $this->savePdfFile($pdfContent);

        $this->saveFileRecord($user, $filePath);

        return $this->createPdfResponse($pdfContent);
    }

    private function handleFileForm($file, $user): Response
    {
        $tempDir = $this->getParameter('kernel.project_dir') . '/public/temp/';
        $publicDir = $this->getParameter('kernel.project_dir') . '/public/temp/';
        $filesystem = new Filesystem();

        if (!$filesystem->exists($tempDir)) {
            $filesystem->mkdir($tempDir);
        }
        if (!$filesystem->exists($publicDir)) {
            $filesystem->mkdir($publicDir);
        }

        $fileName = uniqid('uploaded_', true) . '.' . $file->getClientOriginalExtension();
        $filePath = $tempDir . $fileName;
        $file->move($tempDir, $fileName);

        $pdfContent = $this->pdfService->convertWithLibreOffice($filePath);

        $pdfFileName = uniqid('converted_', true) . '.pdf';
        $pdfPath = $publicDir . $pdfFileName;
        file_put_contents($pdfPath, $pdfContent);

        $this->saveFileRecord($user, $pdfPath);

        return $this->createPdfResponse($pdfContent);
    }


    private function savePdfFile(string $pdfContent): string
    {
        $tempDir = $this->getParameter('kernel.project_dir') . '/public/temp/';
        $fileName = uniqid('pdf_', true) . '.pdf';
        $filePath = $tempDir . $fileName;

        file_put_contents($filePath, $pdfContent);

        return $filePath;
    }

    private function createPdfResponse(string $pdfContent): Response
    {
        return new Response($pdfContent, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="generated.pdf"',
        ]);
    }

    private function saveFileRecord($user, string $filePath): void
    {
        $file = new File();
        $file->setName(basename($filePath));
        $file->setUserkeyId($user);
        $file->setCreatedAt(new DateTimeImmutable());

        $this->entityManager->persist($file);
        $this->entityManager->flush();
    }
}
