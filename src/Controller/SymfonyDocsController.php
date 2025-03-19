<?php

namespace App\Controller;

use App\Service\SymfonyDocs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class SymfonyDocsController extends AbstractController
{
    private SymfonyDocs $symfonyDocs;

    public function __construct(SymfonyDocs $symfonyDocs)
    {
        $this->symfonyDocs = $symfonyDocs;
    }

    #[Route('/symfony-docs', name: 'symfony_docs')]
    public function index(): Response
    {
        $data = $this->symfonyDocs->fetchGitHubInformation(); // Récupération des données GitHub

        return $this->render('symfony_docs/index.html.twig', [
            'controller_name' => 'SymfonyDocsController',
            'github_data' => $data, // On passe les données à Twig
        ]);
    }
}
