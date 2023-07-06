<?php

namespace Src;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class DownloadWebdriverChrome extends AbstractDownloadWebdriver
{
    const BASE_URL_DOWNLOAD = 'https://chromedriver.storage.googleapis.com/';
    const DOWNLOAD_PAGE = 'https://chromedriver.chromium.org/downloads';

    public function handle(string $version, string $path, bool $replaceExisting): string
    {
        // url example https://chromedriver.storage.googleapis.com/114.0.5735.90/chromedriver_win32.zip
        $chromedriver_url = $this->searchVersionLink($version);

        if (!$this->urlIsValid($chromedriver_url)) {
            throw new \Exception('Invalid URL');
        }

        $download_url = self::BASE_URL_DOWNLOAD . $this->getVersionFromUrl($chromedriver_url) . 'chromedriver_win32.zip';
        $filename = basename($download_url);

        $filepath = "{$path}/" . $filename;

        if (!$this->saveFileTo($download_url, $filepath, $replaceExisting)) {
            throw new \Exception("Error saving file from {$download_url} to {$filepath}");
        }

        $this->extractTo($filepath, $path);

        return "{$path}/chromedriver.exe";
    }

    public function extractTo(string $filepath, string $to): bool
    {
        $this->createDirectoryIfNeeded($to);

        if (!is_file($filepath)) {
            throw new \Exception("Error extracting zip file {$filepath}");
        }

        $zip = new \ZipArchive;

        if ($zip->open($filepath) !== TRUE) {
            throw new \Exception("Error opening zip file {$filepath}");
        }

        if (!$zip->extractTo($to)) {
            throw new \Exception("Error extracting zip file {$filepath}");
        }

        $zip->close();

        return true;
    }

    private function saveFileTo(string $download_url, string $pathname, bool $replaceExisting = false): bool
    {
        $path = dirname($pathname);

        $this->createDirectoryIfNeeded($path);

        if (!is_writable($path)) {
            throw new \Exception("Error saving file {$pathname} to {$path}");
        }

        if (is_file($pathname) && $replaceExisting) {
            if (!unlink($pathname)) {
                throw new \Exception("Error deleting file {$pathname}");
            }
        }

        if (is_file($pathname) && !$replaceExisting) {
            throw new \Exception("File {$pathname} already exists, you can try to use downloadTo(force:true)");
        }

        $file_contents = file_get_contents($download_url);

        return file_put_contents($pathname, $file_contents);
    }

    private function createDirectoryIfNeeded(string $path): void
    {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }

    private function urlIsValid(string $url)
    {
        $headers = get_headers($url);
        return stripos($headers[0], "200 OK") ? true : false;
    }

    private function searchVersionLink(string $version)
    {
        $client = new Client();
        $response = $client->get(self::DOWNLOAD_PAGE);
        $crawler = new Crawler($response->getBody()->getContents());

        $links = $crawler->filter('a')
            ->reduce(function ($node) {
                return $node->attr('href') !== null;
            })
            ->reduce(function ($node) {
                return str_contains($node->attr('href'), self::BASE_URL_DOWNLOAD . 'index.html?');
            })
            ->reduce(function ($node) use ($version) {
                return str_contains($this->getVersionFromUrl($node->attr('href')), $version);
            })
            ->each(fn ($node) => $node->attr('href'));

        if (empty($links)) {
            throw new \Exception('Compatible versions not found');
        }

        return count($links) > 1 ? $this->getHigherVersion($links) : $links[0] ?? null;
    }

    private function getHigherVersion(array $links)
    {
        return $links[0] ?? null;
    }

    private function getVersionFromUrl(string $url)
    {
        parse_str(parse_url($url, PHP_URL_QUERY), $query);
        return $query['path'] ?? null;
    }
}
