<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GotenbergService
{
    private string $gotenbergUrl;
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client, string $gotenbergUrl)
    {
        $this->client = $client;
        $this->gotenbergUrl = $gotenbergUrl;
    }


    public function convertUrlToPdf(string $url): string
    {
        $response = $this->client->request('POST', $this->gotenbergUrl . 'forms/chromium/convert/url', [
            'headers' => [
                'Content-Type' => 'multipart/form-data',
            ],
            'body' => [
                'url' => $url, // URL à convertir en PDF
            ],
        ]);

        return $response->getContent(); // Retourner le contenu du PDF
    }


    public function convertHtmlToPdf(string $htmltopdfContent): string
    {
        // Définir un chemin temporaire pour stocker le fichier HTML
        $tempHtmlPath = '/var/www/html/WR602D_mmi22g11/public/index.html';

        // Sauvegarder le HTML dans un fichier temporaire
        file_put_contents($tempHtmlPath, $htmltopdfContent);

        // Envoyer la requête à Gotenberg
        $response = $this->client->request('POST', $this->gotenbergUrl . 'forms/chromium/convert/html', [
            'headers' => [
                'Content-Type' => 'multipart/form-data',
            ],
            'body' => [
                'files' => ['index.html' => fopen($tempHtmlPath, 'r')],
            ],
        ]);

        // Supprimer le fichier temporaire après utilisation
        unlink($tempHtmlPath);

        return $response->getContent();
    }
}
