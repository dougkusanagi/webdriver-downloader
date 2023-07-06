<?php

require_once '../vendor/autoload.php';

use Src\Enums\BrowserEnum;
use Src\WebdriverManager;

(new \Whoops\Run)
    ->pushHandler(new \Whoops\Handler\PrettyPageHandler)
    ->register();

$webdriver_exe_path = (new WebdriverManager)
    ->download(
        browser: BrowserEnum::CHROME,
        path: '/path_to_download',
        force: true
    );

var_dump($webdriver_exe_path);

/** Or you can use a helper */

$webdriver_exe_path = (new WebdriverManager)
    ->downloadChromeWebdriver(
        path: '/path_to_download',
        force: true
    );

var_dump($webdriver_exe_path);
