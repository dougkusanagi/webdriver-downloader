<?php

namespace Src;

use foroco\BrowserDetection;
use Src\Enums\BrowserEnum;

class WebdriverManager
{
    private $download_handlers = [
        BrowserEnum::CHROME->name => DownloadWebdriverChrome::class,
    ];

    public function download(BrowserEnum $browser, string $path = './', bool $force = false): string
    {
        $browser_info = $this->getBrowserInfo();
        $driver = $this->getDriver($browser);
        $webdriver_exe = $driver->handle($browser_info['browser_version'], $path, $force);

        if (!is_file($webdriver_exe)) {
            throw new \Exception("There is no file downloaded");
        }

        return $webdriver_exe;
    }

    private function getDriver(BrowserEnum $browser): AbstractDownloadWebdriver
    {
        return new $this->download_handlers[$browser->name];
    }

    private function getBrowserInfo(): array
    {
        $Browser = new BrowserDetection();
        $browser_info = $Browser->getBrowser($_SERVER['HTTP_USER_AGENT']);
        return $browser_info;
    }
}
