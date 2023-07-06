<?php

namespace Src;

use foroco\BrowserDetection;
use Src\Enums\BrowserEnum;

class WebdriverManager
{
    private string $filename;

    private $drivers = [
        BrowserEnum::CHROME->name => WebdriverManagerChrome::class,
    ];

    public function downloadTo(BrowserEnum $browser, string $path = './', bool $force = false): self
    {
        $browser_info = $this->getBrowserInfo();
        $driver = $this->getDriver($browser);
        $download_url = $driver->getDownloadUrl($browser_info['browser_version']);
        $this->filename = "{$path}/" . basename($download_url);

        if (!$this->saveFileTo($download_url, $this->filename, $force)) {
            throw new \Exception("Error saving file from {$download_url} to {$this->filename}");
        }

        return $this;
    }

    public function extractTo(string $pathname): bool
    {
        $this->createDirectoryIfNeeded($pathname);

        if (!is_file($this->filename)) {
            throw new \Exception("Error extracting zip file {$this->filename}");
        }

        $zip = new \ZipArchive;

        if ($zip->open($this->filename) !== TRUE) {
            throw new \Exception("Error opening zip file {$this->filename}");
        }

        if (!$zip->extractTo($pathname)) {
            throw new \Exception("Error extracting zip file {$this->filename}");
        }

        $zip->close();

        return true;
    }

    private function saveFileTo(string $download_url, string $pathname, bool $force = false): bool
    {
        $path = dirname($pathname);

        $this->createDirectoryIfNeeded($path);

        if (!is_writable($path)) {
            throw new \Exception("Error saving file {$pathname} to {$path}");
        }

        if (is_file($pathname) && $force) {
            if (!unlink($pathname)) {
                throw new \Exception("Error deleting file {$pathname}");
            }
        }

        if (is_file($pathname) && !$force) {
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

    private function getDriver(BrowserEnum $browser): AbstractWebdriverManager
    {
        return new $this->drivers[$browser->name];
    }

    private function getBrowserInfo(): array
    {
        $Browser = new BrowserDetection();
        $browser_info = $Browser->getBrowser($_SERVER['HTTP_USER_AGENT']);
        return $browser_info;
    }
}
