<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Filesystem\Filesystem;

class GotenbergService
{
    private string $gotenbergUrl;
    private HttpClientInterface $client;
    private Filesystem $filesystem;

    public function __construct(HttpClientInterface $client, string $gotenbergUrl, Filesystem $filesystem)
    {
        $this->client = $client;
        $this->gotenbergUrl = $gotenbergUrl;
        $this->filesystem = $filesystem;
    }

    public function convertUrlToPdf(string $url): string
    {
        $response = $this->client->request('POST', $this->gotenbergUrl . 'forms/chromium/convert/url', [
            'headers' => [
                'Content-Type' => 'multipart/form-data',
            ],
            'body' => [
                'url' => $url,
            ],
        ]);

        return $response->getContent();
    }

    public function convertHtmlToPdf(string $htmltopdfContent): string
    {
        $tempHtmlPath = '/var/www/html/WR602D_mmi22g11/public/index.html';

        file_put_contents($tempHtmlPath, $htmltopdfContent);

        $response = $this->client->request('POST', $this->gotenbergUrl . 'forms/chromium/convert/html', [
            'headers' => [
                'Content-Type' => 'multipart/form-data',
            ],
            'body' => [
                'files' => ['index.html' => fopen($tempHtmlPath, 'r')],
            ],
        ]);

        unlink($tempHtmlPath);

        return $response->getContent();
    }

    public function convertWithLibreOffice(string $filePath): string
    {
        $response = $this->client->request('POST', $this->gotenbergUrl . 'forms/libreoffice/convert', [
            'headers' => [
                'Content-Type' => 'multipart/form-data',
            ],
            'body' => [
                'files' => [
                    'document' => fopen($filePath, 'r')
                ],
            ],
        ]);

        $this->cleanTempDirectory($filePath);

        return $response->getContent();
    }

    private function cleanTempDirectory(string $filePath): void
    {
        $directory = dirname($filePath);

        $files = glob($directory . '/*');

        foreach ($files as $file) {
            if (is_file($file) && pathinfo($file, PATHINFO_EXTENSION) !== 'pdf') {
                $this->filesystem->remove($file);
            }
        }
    }
}
