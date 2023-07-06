<?php

namespace Src;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class WebdriverManagerChrome extends AbstractWebdriverManager
{
    const BASE_URL = 'https://chromedriver.storage.googleapis.com/';

    public function getDownloadUrl(string $version): string
    {
        // url example https://chromedriver.storage.googleapis.com/114.0.5735.90/chromedriver_win32.zip
        $chromedriver_url = $this->searchVersionLink($version);

        if (!$this->urlIsValid($chromedriver_url)) {
            throw new \Exception('Invalid URL');
        }

        // dd(self::BASE_URL . $this->getVersion($chromedriver_url) . 'chromedriver_win32.zip');

        return self::BASE_URL . $this->getVersion($chromedriver_url) . 'chromedriver_win32.zip';
    }

    private function urlIsValid(string $url)
    {
        $headers = get_headers($url);
        return stripos($headers[0], "200 OK") ? true : false;
    }

    private function searchVersionLink(string $version)
    {
        $client = new Client();
        $response = $client->get('https://chromedriver.chromium.org/downloads');
        $crawler = new Crawler($response->getBody()->getContents());

        $links = $crawler->filter('a')
            ->reduce(function ($node) {
                return $node->attr('href') !== null;
            })
            ->reduce(function ($node) {
                return str_contains($node->attr('href'), 'https://chromedriver.storage.googleapis.com/index.html?');
            })
            ->reduce(function ($node) use ($version) {
                return str_contains($this->getVersion($node->attr('href')), $version);
            })
            ->each(fn ($node) => $node->attr('href'));

        if (empty($links)) {
            throw new \Exception('Version not found');
        }

        return count($links) > 1 ? $this->getHigherVersion($links) : $links[0] ?? null;
    }

    private function getHigherVersion(array $links)
    {
        return $links[0] ?? null;
    }

    private function getVersion(string $url)
    {
        parse_str(parse_url($url, PHP_URL_QUERY), $query);
        return $query['path'] ?? null;
    }
}
