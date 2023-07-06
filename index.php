<?php

require_once 'vendor/autoload.php';

use Src\Enums\BrowserEnum;
use Src\WebdriverManager;

function dump($var)
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

function dd($var)
{
    dump($var);
    die();
}

$whoops = new \Whoops\Run;
$whoops->prependHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

$path = __DIR__ . '/chromedriver';

(new WebdriverManager)
    ->downloadTo(BrowserEnum::CHROME, $path, true)
    ->extractTo($path);
